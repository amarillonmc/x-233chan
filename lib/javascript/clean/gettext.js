/*
Pure Javascript implementation of Uniforum message translation.
Copyright (C) 2008 Joshua I. Miller <unrtst@cpan.org>, all rights reserved

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU Library General Public License as published
by the Free Software Foundation; either version 2, or (at your option)
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Library General Public License for more details.

You should have received a copy of the GNU Library General Public
License along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
USA.

=head1 NAME

Javascript Gettext - Javascript implemenation of GNU Gettext API.
*/

Gettext = function (args) {
    this.domain         = 'messages';
    // locale_data will be populated from <link...> if not specified in args
    this.locale_data    = undefined;

    // set options
    var options = [ "domain", "locale_data" ];
    if (this.isValidObject(args)) {
        for (var i in args) {
            for (var j=0; j<options.length; j++) {
                if (i == options[j]) {
                    // don't set it if it's null or undefined
                    if (this.isValidObject(args[i]))
                        this[i] = args[i];
                }
            }
        }
    }


    // try to load the lang file from somewhere
    this.try_load_lang();

    return this;
}

Gettext.context_glue = "\004";
Gettext._locale_data = {};

Gettext.prototype.try_load_lang = function() {
    // check to see if language is statically included
    if (typeof(this.locale_data) != 'undefined') {
        // we're going to reformat it, and overwrite the variable
        var locale_copy = this.locale_data;
        this.locale_data = undefined;
        this.parse_locale_data(locale_copy);

        if (typeof(Gettext._locale_data[this.domain]) == 'undefined') {
            throw new Error("Error: Gettext 'locale_data' does not contain the domain '"+this.domain+"'");
        }
    }


    // try loading from JSON
    // get lang links
    var lang_link = this.get_lang_refs();

    if (typeof(lang_link) == 'object' && lang_link.length > 0) {
        // NOTE: there will be a delay here, as this is async.
        // So, any i18n calls made right after page load may not
        // get translated.
        // XXX: we may want to see if we can "fix" this behavior
        for (var i=0; i<lang_link.length; i++) {
            var link = lang_link[i];
            if (link.type == 'application/json') {
                if (! this.try_load_lang_json(link.href) ) {
                    throw new Error("Error: Gettext 'try_load_lang_json' failed. Unable to exec xmlhttprequest for link ["+link.href+"]");
                }
            } else if (link.type == 'application/x-po') {
                if (! this.try_load_lang_po(link.href) ) {
                    throw new Error("Error: Gettext 'try_load_lang_po' failed. Unable to exec xmlhttprequest for link ["+link.href+"]");
                }
            } else {
                // TODO: implement the other types (.mo)
                throw new Error("TODO: link type ["+link.type+"] found, and support is planned, but not implemented at this time.");
            }
        }
    }
};

// This takes the bin/po2json'd data, and moves it into an internal form
// for use in our lib, and puts it in our object as:
//  Gettext._locale_data = {
//      domain : {
//          head : { headfield : headvalue },
//          msgs : {
//              msgid : [ msgid_plural, msgstr, msgstr_plural ],
//          },
Gettext.prototype.parse_locale_data = function(locale_data) {
    if (typeof(Gettext._locale_data) == 'undefined') {
        Gettext._locale_data = { };
    }

    // suck in every domain defined in the supplied data
    for (var domain in locale_data) {
        // skip empty specs (flexibly)
        if ((! locale_data.hasOwnProperty(domain)) || (! this.isValidObject(locale_data[domain])))
            continue;
        // skip if it has no msgid's
        var has_msgids = false;
        for (var msgid in locale_data[domain]) {
            has_msgids = true;
            break;
        }
        if (! has_msgids) continue;

        // grab shortcut to data
        var data = locale_data[domain];

        // if they specifcy a blank domain, default to "messages"
        if (domain == "") domain = "messages";
        // init the data structure
        if (! this.isValidObject(Gettext._locale_data[domain]) )
            Gettext._locale_data[domain] = { };
        if (! this.isValidObject(Gettext._locale_data[domain].head) )
            Gettext._locale_data[domain].head = { };
        if (! this.isValidObject(Gettext._locale_data[domain].msgs) )
            Gettext._locale_data[domain].msgs = { };

        for (var key in data) {
            if (key == "") {
                var header = data[key];
                for (var head in header) {
                    var h = head.toLowerCase();
                    Gettext._locale_data[domain].head[h] = header[head];
                }
            } else {
                Gettext._locale_data[domain].msgs[key] = data[key];
            }
        }
    }

    // build the plural forms function
    for (var domain in Gettext._locale_data) {
        if (this.isValidObject(Gettext._locale_data[domain].head['plural-forms']) &&
            typeof(Gettext._locale_data[domain].head.plural_func) == 'undefined') {
            // untaint data
            var plural_forms = Gettext._locale_data[domain].head['plural-forms'];
            var pf_re = new RegExp('^(\\s*nplurals\\s*=\\s*[0-9]+\\s*;\\s*plural\\s*=\\s*(?:\\s|[-\\?\\|&=!<>+*/%:;a-zA-Z0-9_\(\)])+)', 'm');
            if (pf_re.test(plural_forms)) {
                //ex english: "Plural-Forms: nplurals=2; plural=(n != 1);\n"
                //pf = "nplurals=2; plural=(n != 1);";
                //ex russian: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10< =4 && (n%100<10 or n%100>=20) ? 1 : 2)
                //pf = "nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)";

                var pf = Gettext._locale_data[domain].head['plural-forms'];
                if (! /;\s*$/.test(pf)) pf = pf.concat(';');
                /* We used to use eval, but it seems IE has issues with it.
                 * We now use "new Function", though it carries a slightly
                 * bigger performance hit.
                var code = 'function (n) { var plural; var nplurals; '+pf+' return { "nplural" : nplurals, "plural" : (plural === true ? 1 : plural ? plural : 0) }; };';
                Gettext._locale_data[domain].head.plural_func = eval("("+code+")");
                */
                var code = 'var plural; var nplurals; '+pf+' return { "nplural" : nplurals, "plural" : (plural === true ? 1 : plural ? plural : 0) };';
                Gettext._locale_data[domain].head.plural_func = new Function("n", code);
            } else {
                throw new Error("Syntax error in language file. Plural-Forms header is invalid ["+plural_forms+"]");
            }   

        // default to english plural form
        } else if (typeof(Gettext._locale_data[domain].head.plural_func) == 'undefined') {
            Gettext._locale_data[domain].head.plural_func = function (n) {
                var p = (n != 1) ? 1 : 0;
                return { 'nplural' : 2, 'plural' : p };
                };
        } // else, plural_func already created
    }

    return;
};


// try_load_lang_po : do an ajaxy call to load in the .po lang defs
Gettext.prototype.try_load_lang_po = function(uri) {
    var data = this.sjax(uri);
    if (! data) return;

    var domain = this.uri_basename(uri);
    var parsed = this.parse_po(data);

    var rv = {};
    // munge domain into/outof header
    if (parsed) {
        if (! parsed[""]) parsed[""] = {};
        if (! parsed[""]["domain"]) parsed[""]["domain"] = domain;
        domain = parsed[""]["domain"];
        rv[domain] = parsed;

        this.parse_locale_data(rv);
    }

    return 1;
};

Gettext.prototype.uri_basename = function(uri) {
    var rv;
    if (rv = uri.match(/^(.*\/)?(.*)/)) {
        var ext_strip;
        if (ext_strip = rv[2].match(/^(.*)\..+$/))
            return ext_strip[1];
        else
            return rv[2];
    } else {
        return "";
    }
};

Gettext.prototype.parse_po = function(data) {
    var rv = {};
    var buffer = {};
    var lastbuffer = "";
    var errors = [];
    var lines = data.split("\n");
    for (var i=0; i<lines.length; i++) {
        // chomp
        lines[i] = lines[i].replace(/(\n|\r)+$/, '');

        var match;

        // Empty line / End of an entry.
        if (/^$/.test(lines[i])) {
            if (typeof(buffer['msgid']) != 'undefined') {
                var msg_ctxt_id = (typeof(buffer['msgctxt']) != 'undefined' &&
                                   buffer['msgctxt'].length) ?
                                  buffer['msgctxt']+Gettext.context_glue+buffer['msgid'] :
                                  buffer['msgid'];
                var msgid_plural = (typeof(buffer['msgid_plural']) != 'undefined' &&
                                    buffer['msgid_plural'].length) ?
                                   buffer['msgid_plural'] :
                                   null;

                // find msgstr_* translations and push them on
                var trans = [];
                for (var str in buffer) {
                    var match;
                    if (match = str.match(/^msgstr_(\d+)/))
                        trans[parseInt(match[1])] = buffer[str];
                }
                trans.unshift(msgid_plural);

                // only add it if we've got a translation
                // NOTE: this doesn't conform to msgfmt specs
                if (trans.length > 1) rv[msg_ctxt_id] = trans;

                buffer = {};
                lastbuffer = "";
            }

        // comments
        } else if (/^#/.test(lines[i])) {
            continue;

        // msgctxt
        } else if (match = lines[i].match(/^msgctxt\s+(.*)/)) {
            lastbuffer = 'msgctxt';
            buffer[lastbuffer] = this.parse_po_dequote(match[1]);

        // msgid
        } else if (match = lines[i].match(/^msgid\s+(.*)/)) {
            lastbuffer = 'msgid';
            buffer[lastbuffer] = this.parse_po_dequote(match[1]);

        // msgid_plural
        } else if (match = lines[i].match(/^msgid_plural\s+(.*)/)) {
            lastbuffer = 'msgid_plural';
            buffer[lastbuffer] = this.parse_po_dequote(match[1]);

        // msgstr
        } else if (match = lines[i].match(/^msgstr\s+(.*)/)) {
            lastbuffer = 'msgstr_0';
            buffer[lastbuffer] = this.parse_po_dequote(match[1]);

        // msgstr[0] (treak like msgstr)
        } else if (match = lines[i].match(/^msgstr\[0\]\s+(.*)/)) {
            lastbuffer = 'msgstr_0';
            buffer[lastbuffer] = this.parse_po_dequote(match[1]);

        // msgstr[n]
        } else if (match = lines[i].match(/^msgstr\[(\d+)\]\s+(.*)/)) {
            lastbuffer = 'msgstr_'+match[1];
            buffer[lastbuffer] = this.parse_po_dequote(match[2]);

        // continued string
        } else if (/^"/.test(lines[i])) {
            buffer[lastbuffer] += this.parse_po_dequote(lines[i]);

        // something strange
        } else {
            errors.push("Strange line ["+i+"] : "+lines[i]);
        }
    }


    // handle the final entry
    if (typeof(buffer['msgid']) != 'undefined') {
        var msg_ctxt_id = (typeof(buffer['msgctxt']) != 'undefined' &&
                           buffer['msgctxt'].length) ?
                          buffer['msgctxt']+Gettext.context_glue+buffer['msgid'] :
                          buffer['msgid'];
        var msgid_plural = (typeof(buffer['msgid_plural']) != 'undefined' &&
                            buffer['msgid_plural'].length) ?
                           buffer['msgid_plural'] :
                           null;

        // find msgstr_* translations and push them on
        var trans = [];
        for (var str in buffer) {
            var match;
            if (match = str.match(/^msgstr_(\d+)/))
                trans[parseInt(match[1])] = buffer[str];
        }
        trans.unshift(msgid_plural);

        // only add it if we've got a translation
        // NOTE: this doesn't conform to msgfmt specs
        if (trans.length > 1) rv[msg_ctxt_id] = trans;

        buffer = {};
        lastbuffer = "";
    }


    // parse out the header
    if (rv[""] && rv[""][1]) {
        var cur = {};
        var hlines = rv[""][1].split(/\\n/);
        for (var i=0; i<hlines.length; i++) {
            if (! hlines.length) continue;

            var pos = hlines[i].indexOf(':', 0);
            if (pos != -1) {
                var key = hlines[i].substring(0, pos);
                var val = hlines[i].substring(pos +1);
                var keylow = key.toLowerCase();

                if (cur[keylow] && cur[keylow].length) {
                    errors.push("SKIPPING DUPLICATE HEADER LINE: "+hlines[i]);
                } else if (/#-#-#-#-#/.test(keylow)) {
                    errors.push("SKIPPING ERROR MARKER IN HEADER: "+hlines[i]);
                } else {
                    // remove begining spaces if any
                    val = val.replace(/^\s+/, '');
                    cur[keylow] = val;
                }

            } else {
                errors.push("PROBLEM LINE IN HEADER: "+hlines[i]);
                cur[hlines[i]] = '';
            }
        }

        // replace header string with assoc array
        rv[""] = cur;
    } else {
        rv[""] = {};
    }

    // TODO: XXX: if there are errors parsing, what do we want to do?
    // GNU Gettext silently ignores errors. So will we.
    // alert( "Errors parsing po file:\n" + errors.join("\n") );

    return rv;
};


Gettext.prototype.parse_po_dequote = function(str) {
    var match;
    if (match = str.match(/^"(.*)"/)) {
        str = match[1];
    }
    str = str.replace(/\\"/, "");
    return str;
};


// try_load_lang_json : do an ajaxy call to load in the lang defs
Gettext.prototype.try_load_lang_json = function(uri) {
    var data = this.sjax(uri);
    if (! data) return;

    var rv = this.JSON(data);
    this.parse_locale_data(rv);

    return 1;
};

// this finds all <link> tags, filters out ones that match our
// specs, and returns a list of hashes of those
Gettext.prototype.get_lang_refs = function() {
    var langs = new Array();
    var links = document.getElementsByTagName("link");
    // find all <link> tags in dom; filter ours
    for (var i=0; i<links.length; i++) {
        if (links[i].rel == 'gettext' && links[i].href) {
            if (typeof(links[i].type) == 'undefined' ||
                links[i].type == '') {
                if (/\.json$/i.test(links[i].href)) {
                    links[i].type = 'application/json';
                } else if (/\.js$/i.test(links[i].href)) {
                    links[i].type = 'application/json';
                } else if (/\.po$/i.test(links[i].href)) {
                    links[i].type = 'application/x-po';
                } else if (/\.mo$/i.test(links[i].href)) {
                    links[i].type = 'application/x-mo';
                } else {
                    throw new Error("LINK tag with rel=gettext found, but the type and extension are unrecognized.");
                }
            }

            links[i].type = links[i].type.toLowerCase();
            if (links[i].type == 'application/json') {
                links[i].type = 'application/json';
            } else if (links[i].type == 'text/javascript') {
                links[i].type = 'application/json';
            } else if (links[i].type == 'application/x-po') {
                links[i].type = 'application/x-po';
            } else if (links[i].type == 'application/x-mo') {
                links[i].type = 'application/x-mo';
            } else {
                throw new Error("LINK tag with rel=gettext found, but the type attribute ["+links[i].type+"] is unrecognized.");
            }

            langs.push(links[i]);
        }
    }
    return langs;
};


/*

=head2 textdomain( domain )

Set domain for future gettext() calls

A  message  domain  is  a  set of translatable msgid messages. Usually,
every software package has its own message domain. The domain  name  is
used to determine the message catalog where a translation is looked up;
it must be a non-empty string.

The current message domain is used by the gettext, ngettext, pgettext,
npgettext functions, and by the dgettext, dcgettext, dngettext, dcngettext,
dpgettext, dcpgettext, dnpgettext and dcnpgettext functions when called
with a NULL domainname argument.

If domainname is not NULL, the current message domain is set to
domainname.

If domainname is undefined, null, or empty string, the function returns
the current message domain.

If  successful,  the  textdomain  function  returns the current message
domain, after possibly changing it. (ie. if you set a new domain, the 
value returned will NOT be the previous domain).

=cut

*/
Gettext.prototype.textdomain = function (domain) {
    if (domain && domain.length) this.domain = domain;
    return this.domain;
}

/*

=head2 gettext( MSGID )

Returns the translation for B<MSGID>.  Example:

    alert( gt.gettext("Hello World!\n") );

If no translation can be found, the unmodified B<MSGID> is returned,
i. e. the function can I<never> fail, and will I<never> mess up your
original message.

One common mistake is to interpolate a variable into the string like this:

  var translated = gt.gettext("Hello " + full_name);

The interpolation will happen before it's passed to gettext, and it's 
unlikely you'll have a translation for every "Hello Tom" and "Hello Dick"
and "Hellow Harry" that may arise.

Use C<strargs()> (see below) to solve this problem:

  var translated = Gettext.strargs( gt.gettext("Hello %1"), [full_name] );

This is espeically useful when multiple replacements are needed, as they 
may not appear in the same order within the translation. As an English to
French example:

  Expected result: "This is the red ball"
  English: "This is the %1 %2"
  French:  "C'est le %2 %1"
  Code: Gettext.strargs( gt.gettext("This is the %1 %2"), ["red", "ball"] );

(The example is stupid because neither color nor thing will get
translated here ...).

=head2 dgettext( TEXTDOMAIN, MSGID )

Like gettext(), but retrieves the message for the specified 
B<TEXTDOMAIN> instead of the default domain.  In case you wonder what
a textdomain is, see above section on the textdomain() call.

=head2 dcgettext( TEXTDOMAIN, MSGID, CATEGORY )

Like dgettext() but retrieves the message from the specified B<CATEGORY>
instead of the default category C<LC_MESSAGES>.

NOTE: the categories are really useless in javascript context. This is
here for GNU Gettext API compatability. In practice, you'll never need
to use this. This applies to all the calls including the B<CATEGORY>.


=head2 ngettext( MSGID, MSGID_PLURAL, COUNT )

Retrieves the correct translation for B<COUNT> items.  In legacy software
you will often find something like:

    alert( count + " file(s) deleted.\n" );

or

    printf(count + " file%s deleted.\n", $count == 1 ? '' : 's');

I<NOTE: javascript lacks a builtin printf, so the above isn't a working example>

The first example looks awkward, the second will only work in English
and languages with similar plural rules.  Before ngettext() was introduced,
the best practice for internationalized programs was:

    if (count == 1) {
        alert( gettext("One file deleted.\n") );
    } else {
        printf( gettext("%d files deleted.\n"), count );
    }

This is a nuisance for the programmer and often still not sufficient
for an adequate translation.  Many languages have completely different
ideas on numerals.  Some (French, Italian, ...) treat 0 and 1 alike,
others make no distinction at all (Japanese, Korean, Chinese, ...),
others have two or more plural forms (Russian, Latvian, Czech,
Polish, ...).  The solution is:

    printf( ngettext("One file deleted.\n",
                     "%d files deleted.\n",
                     count), // argument to ngettext!
            count);          // argument to printf!

In English, or if no translation can be found, the first argument
(B<MSGID>) is picked if C<count> is one, the second one otherwise.
For other languages, the correct plural form (of 1, 2, 3, 4, ...)
is automatically picked, too.  You don't have to know anything about
the plural rules in the target language, ngettext() will take care
of that.

This is most of the time sufficient but you will have to prove your
creativity in cases like

    "%d file(s) deleted, and %d file(s) created.\n"

That said, javascript lacks C<printf()> support. Supplied with Gettext.js
is the C<strargs()> method, which can be used for these cases:

    Gettext.strargs( gt.ngettext( "One file deleted.\n",
                                  "%d files deleted.\n",
                                  count), // argument to ngettext!
                     count); // argument to strargs!

NOTE: the variable replacement isn't done for you, so you must
do it yourself as in the above.

=head2 dngettext( TEXTDOMAIN, MSGID, MSGID_PLURAL, COUNT )

Like ngettext() but retrieves the translation from the specified
textdomain instead of the default domain.

=head2 dcngettext( TEXTDOMAIN, MSGID, MSGID_PLURAL, COUNT, CATEGORY )

Like dngettext() but retrieves the translation from the specified
category, instead of the default category C<LC_MESSAGES>.


=head2 pgettext( MSGCTXT, MSGID )

Returns the translation of MSGID, given the context of MSGCTXT.

Both items are used as a unique key into the message catalog.

This allows the translator to have two entries for words that may
translate to different foreign words based on their context. For
example, the word "View" may be a noun or a verb, which may be
used in a menu as File->View or View->Source.

    alert( pgettext( "Verb: To View", "View" ) );
    alert( pgettext( "Noun: A View", "View"  ) );

The above will both lookup different entries in the message catalog.

In English, or if no translation can be found, the second argument
(B<MSGID>) is returned.

=head2 dpgettext( TEXTDOMAIN, MSGCTXT, MSGID )

Like pgettext(), but retrieves the message for the specified 
B<TEXTDOMAIN> instead of the default domain.

=head2 dcpgettext( TEXTDOMAIN, MSGCTXT, MSGID, CATEGORY )

Like dpgettext() but retrieves the message from the specified B<CATEGORY>
instead of the default category C<LC_MESSAGES>.


=head2 npgettext( MSGCTXT, MSGID, MSGID_PLURAL, COUNT )

Like ngettext() with the addition of context as in pgettext().

In English, or if no translation can be found, the second argument
(MSGID) is picked if B<COUNT> is one, the third one otherwise.

=head2 dnpgettext( TEXTDOMAIN, MSGCTXT, MSGID, MSGID_PLURAL, COUNT )

Like npgettext() but retrieves the translation from the specified
textdomain instead of the default domain.

=head2 dcnpgettext( TEXTDOMAIN, MSGCTXT, MSGID, MSGID_PLURAL, COUNT, CATEGORY )

Like dnpgettext() but retrieves the translation from the specified
category, instead of the default category C<LC_MESSAGES>.

=cut

*/

// gettext
Gettext.prototype.gettext = function (msgid) {
    var msgctxt;
    var msgid_plural;
    var n;
    var category;
    return this.dcnpgettext(null, msgctxt, msgid, msgid_plural, n, category);
};

Gettext.prototype.dgettext = function (domain, msgid) {
    var msgctxt;
    var msgid_plural;
    var n;
    var category;
    return this.dcnpgettext(domain, msgctxt, msgid, msgid_plural, n, category);
};

Gettext.prototype.dcgettext = function (domain, msgid, category) {
    var msgctxt;
    var msgid_plural;
    var n;
    return this.dcnpgettext(domain, msgctxt, msgid, msgid_plural, n, category);
};

// ngettext
Gettext.prototype.ngettext = function (msgid, msgid_plural, n) {
    var msgctxt;
    var category;
    return this.dcnpgettext(null, msgctxt, msgid, msgid_plural, n, category);
};

Gettext.prototype.dngettext = function (domain, msgid, msgid_plural, n) {
    var msgctxt;
    var category;
    return this.dcnpgettext(domain, msgctxt, msgid, msgid_plural, n, category);
};

Gettext.prototype.dcngettext = function (domain, msgid, msgid_plural, n, category) {
    var msgctxt;
    return this.dcnpgettext(domain, msgctxt, msgid, msgid_plural, n, category, category);
};

// pgettext
Gettext.prototype.pgettext = function (msgctxt, msgid) {
    var msgid_plural;
    var n;
    var category;
    return this.dcnpgettext(null, msgctxt, msgid, msgid_plural, n, category);
};

Gettext.prototype.dpgettext = function (domain, msgctxt, msgid) {
    var msgid_plural;
    var n;
    var category;
    return this.dcnpgettext(domain, msgctxt, msgid, msgid_plural, n, category);
};

Gettext.prototype.dcpgettext = function (domain, msgctxt, msgid, category) {
    var msgid_plural;
    var n;
    return this.dcnpgettext(domain, msgctxt, msgid, msgid_plural, n, category);
};

// npgettext
Gettext.prototype.npgettext = function (msgctxt, msgid, msgid_plural, n) {
    var category;
    return this.dcnpgettext(null, msgctxt, msgid, msgid_plural, n, category);
};

Gettext.prototype.dnpgettext = function (domain, msgctxt, msgid, msgid_plural, n) {
    var category;
    return this.dcnpgettext(domain, msgctxt, msgid, msgid_plural, n, category);
};

// this has all the options, so we use it for all of them.
Gettext.prototype.dcnpgettext = function (domain, msgctxt, msgid, msgid_plural, n, category) {
    if (! this.isValidObject(msgid)) return '';

    var plural = this.isValidObject(msgid_plural);
    var msg_ctxt_id = this.isValidObject(msgctxt) ? msgctxt+Gettext.context_glue+msgid : msgid;

    var domainname = this.isValidObject(domain)      ? domain :
                     this.isValidObject(this.domain) ? this.domain :
                                                       'messages';

    // category is always LC_MESSAGES. We ignore all else
    var category_name = 'LC_MESSAGES';
    var category = 5;

    var locale_data = new Array();
    if (typeof(Gettext._locale_data) != 'undefined' &&
        this.isValidObject(Gettext._locale_data[domainname])) {
        locale_data.push( Gettext._locale_data[domainname] );

    } else if (typeof(Gettext._locale_data) != 'undefined') {
        // didn't find domain we're looking for. Search all of them.
        for (var dom in Gettext._locale_data) {
            locale_data.push( Gettext._locale_data[dom] );
        }
    }

    var trans = [];
    var found = false;
    var domain_used; // so we can find plural-forms if needed
    if (locale_data.length) {
        for (var i=0; i<locale_data.length; i++) {
            var locale = locale_data[i];
            if (this.isValidObject(locale.msgs[msg_ctxt_id])) {
                // make copy of that array (cause we'll be destructive)
                for (var j=0; j<locale.msgs[msg_ctxt_id].length; j++) {
                    trans[j] = locale.msgs[msg_ctxt_id][j];
                }
                trans.shift(); // throw away the msgid_plural
                domain_used = locale;
                found = true;
                // only break if found translation actually has a translation.
                if ( trans.length > 0 && trans[0].length != 0 )
                    break;
            }
        }
    }

    // default to english if we lack a match, or match has zero length
    if ( trans.length == 0 || trans[0].length == 0 ) {
        trans = [ msgid, msgid_plural ];
    }

    var translation = trans[0];
    if (plural) {
        var p;
        if (found && this.isValidObject(domain_used.head.plural_func) ) {
            var rv = domain_used.head.plural_func(n);
            if (! rv.plural) rv.plural = 0;
            if (! rv.nplural) rv.nplural = 0;
            // if plurals returned is out of bound for total plural forms
            if (rv.nplural <= rv.plural) rv.plural = 0;
            p = rv.plural;
        } else {
            p = (n != 1) ? 1 : 0;
        }
        if (this.isValidObject(trans[p]))
            translation = trans[p];
    }

    return translation;
};


/*

=head2 strargs (string, argument_array)

  string : a string that potentially contains formatting characters.
  argument_array : an array of positional replacement values

This is a utility method to provide some way to support positional parameters within a string, as javascript lacks a printf() method.

The format is similar to printf(), but greatly simplified (ie. fewer features).

Any percent signs followed by numbers are replaced with the corrosponding item from the B<argument_array>.

Example:

    var string = "%2 roses are red, %1 violets are blue";
    var args   = new Array("10", "15");
    var result = Gettext.strargs(string, args);
    // result is "15 roses are red, 10 violets are blue"

The format numbers are 1 based, so the first itme is %1.

A lone percent sign may be escaped by preceeding it with another percent sign.

A percent sign followed by anything other than a number or another percent sign will be passed through as is.

Some more examples should clear up any abmiguity. The following were called with the orig string, and the array as Array("[one]", "[two]") :

  orig string "blah" becomes "blah"
  orig string "" becomes ""
  orig string "%%" becomes "%"
  orig string "%%%" becomes "%%"
  orig string "%%%%" becomes "%%"
  orig string "%%%%%" becomes "%%%"
  orig string "tom%%dick" becomes "tom%dick"
  orig string "thing%1bob" becomes "thing[one]bob"
  orig string "thing%1%2bob" becomes "thing[one][two]bob"
  orig string "thing%1asdf%2asdf" becomes "thing[one]asdf[two]asdf"
  orig string "%1%2%3" becomes "[one][two]"
  orig string "tom%1%%2%aDick" becomes "tom[one]%2%aDick"

This is especially useful when using plurals, as the string will nearly always contain the number.

It's also useful in translated strings where the translator may have needed to move the position of the parameters.

For example:

  var count = 14;
  Gettext.strargs( gt.ngettext('one banana', '%1 bananas', count), [count] );

NOTE: this may be called as an instance method, or as a class method.

  // instance method:
  var gt = new Gettext(params);
  gt.strargs(string, args);

  // class method:
  Gettext.strargs(string, args);

=cut

*/
/* utility method, since javascript lacks a printf */
Gettext.strargs = function (str, args) {
    // make sure args is an array
    if ( null == args ||
         'undefined' == typeof(args) ) {
        args = [];
    } else if (args.constructor != Array) {
        args = [args];
    }

    // NOTE: javascript lacks support for zero length negative look-behind
    // in regex, so we must step through w/ index.
    // The perl equiv would simply be:
    //    $string =~ s/(?<!\%)\%([0-9]+)/$args[$1]/g;
    //    $string =~ s/\%\%/\%/g; # restore escaped percent signs

    var newstr = "";
    while (true) {
        var i = str.indexOf('%');
        var match_n;

        // no more found. Append whatever remains
        if (i == -1) {
            newstr += str;
            break;
        }

        // we found it, append everything up to that
        newstr += str.substr(0, i);

        // check for escpaed %%
        if (str.substr(i, 2) == '%%') {
            newstr += '%';
            str = str.substr((i+2));

        // % followed by number
        } else if ( match_n = str.substr(i).match(/^%(\d+)/) ) {
            var arg_n = parseInt(match_n[1]);
            var length_n = match_n[1].length;
            if ( arg_n > 0 && args[arg_n -1] != null && typeof(args[arg_n -1]) != 'undefined' )
                newstr += args[arg_n -1];
            str = str.substr( (i + 1 + length_n) );

        // % followed by some other garbage - just remove the %
        } else {
            newstr += '%';
            str = str.substr((i+1));
        }
    }

    return newstr;
}

/* instance method wrapper of strargs */
Gettext.prototype.strargs = function (str, args) {
    return Gettext.strargs(str, args);
}

/* verify that something is an array */
Gettext.prototype.isArray = function (thisObject) {
    return this.isValidObject(thisObject) && thisObject.constructor == Array;
};

/* verify that an object exists and is valid */
Gettext.prototype.isValidObject = function (thisObject) {
    if (null == thisObject) {
        return false;
    } else if ('undefined' == typeof(thisObject) ) {
        return false;
    } else {
        return true;
    }
};

Gettext.prototype.sjax = function (uri) {
    var xmlhttp;
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else if (navigator.userAgent.toLowerCase().indexOf('msie 5') != -1) {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    } else {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    }

    if (! xmlhttp)
        throw new Error("Your browser doesn't do Ajax. Unable to support external language files.");

    xmlhttp.open('GET', uri, false);
    try { xmlhttp.send(null); }
    catch (e) { return; }

    // we consider status 200 and 0 as ok.
    // 0 happens when we request local file, allowing this to run on local files
    var sjax_status = xmlhttp.status;
    if (sjax_status == 200 || sjax_status == 0) {
        return xmlhttp.responseText;
    } else {
        var error = xmlhttp.statusText + " (Error " + xmlhttp.status + ")";
        if (xmlhttp.responseText.length) {
            error += "\n" + xmlhttp.responseText;
        }
        alert(error);
        return;
    }
}

Gettext.prototype.JSON = function (data) {
    return eval('(' + data + ')');
}
