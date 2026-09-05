// Run with: node --test tests/moderation-cookies.test.js
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

function loadScript(filename, cookie, legacy) {
	const nodes = {
		postform: { board: { value: 'b' } },
		passwordbox: { innerHTML: '<td></td><td></td>' },
		fileonly: { parentNode: { innerHTML: '' } }
	};
	const posts = ['dnb-b-123-y', 'dnb-b-124-n'].map(id => ({
		getAttribute: name => name === 'id' ? id : null,
		innerHTML: ''
	}));
	const requests = [];
	const context = vm.createContext({
		document: {
			cookie,
			addEventListener: () => {}, // The deployed bundle also registers ID coloring.
			getElementById: id => nodes[id] || null,
			getElementsByTagName: tag => tag === 'span' ? posts : [],
			location: { toString: () => 'https://board.example/b/' }
		},
		window: {},
		navigator: { userAgent: 'Safari', appName: 'Netscape' },
		Gettext: function () { this.gettext = message => message; },
		Ajax: { Request: function (url) { requests.push(url); } },
		ku_boardspath: 'https://board.example',
		ku_cgipath: 'https://board.example'
	});
	if (legacy) {
		vm.runInContext('delete String.prototype.replaceAll', context);
	} else {
		assert.equal(vm.runInContext('typeof String.prototype.replaceAll', context), 'function');
	}
	vm.runInContext(fs.readFileSync(path.join(__dirname, '..', filename), 'utf8'), context, { filename });
	return { context, nodes, posts, requests };
}

for (const filename of ['lib/javascript/clean/kusaba.js', 'lib/javascript/kusaba.js']) {
	for (const legacy of [false, true]) {
		const label = `${filename} (${legacy ? 'without' : 'with'} native replaceAll)`;

		test(`${label}: reads cookie values and preserves existing decoding`, () => {
			const { context } = loadScript(filename,
				'kumod=allboards; name=%E5%8C%BF%E5%90%8D+%E7%94%A8%E6%88%B7; email=a%2Bb%40example.org; kustyle=futaba; empty=', legacy);
			assert.equal(context.getCookie('kumod'), 'allboards');
			assert.equal(context.getCookie('name'), '匿名 用户');
			assert.equal(context.getCookie('email'), 'a+b@example.org');
			assert.equal(context.getCookie('kustyle'), 'futaba');
			assert.equal(context.getCookie('empty'), '');
			assert.equal(context.getCookie('missing'), '');
			context.document.cookie = 'notkumod=allboards; kumod=a%7Cb%7Cc';
			assert.equal(context.getCookie('kumod'), 'a|b|c');
			context.document.cookie = 'notkumod=allboards';
			assert.equal(context.getCookie('kumod'), '');
		});

		for (const [role, cookie, allowed] of [
			['administrator', 'kumod=allboards; kustyle=futaba', true],
			['assigned moderator', 'kustyle=futaba; kumod=a%7Cb%7Cc', true],
			['moderator of another board', 'kumod=a%7Cbb%7Cc', false],
			['visitor', 'kustyle=futaba', false],
			['logged-out user', '', false]
		]) {
			test(`${label}: initializes board controls for ${role}`, () => {
				const { context, nodes, posts, requests } = loadScript(filename, cookie, legacy);
				context.window.onload();
				assert.equal(context.kumod_set, allowed);
				assert.equal(nodes.passwordbox.innerHTML.includes('name="modpassword"'), allowed);
				assert.equal(nodes.passwordbox.innerHTML.includes('name="displaystaffstatus"'), allowed);
				assert.equal(nodes.fileonly.parentNode.innerHTML.includes('name="moddelete"'), allowed);
				assert.equal(nodes.fileonly.parentNode.innerHTML.includes('name="modban"'), allowed);
				assert.equal(posts[0].innerHTML.includes('delthreadid=123'), allowed);
				assert.equal(posts[1].innerHTML.includes('delpostid=124'), allowed);
				assert.equal(requests.length, allowed ? 2 : 0);
			});
		}
	}
}
