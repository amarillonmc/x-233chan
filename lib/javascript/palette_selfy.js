// palette_selfy.js .. for PaintBBS and ShiPainter .. last update : 2004/04/11.

//→使い方 .. 外部JSとして読み込んでから、好きな所で palette_selfy() を呼び出して下さい.
		var selfv=new Array();	var selfytag=new Array();	//←消さないで.

//↓設定 ------------------------------------------------------------
//  ※selfv[*] は、それぞれの設定を空白にすると、その機能のボタンを表示させなくできます.

// +-する値のとこ
var pnum = 10;	// +- のデフォルト値
selfv[0] = 'size=3 style="text-align:right">';	// 数値タグ(type=text)の中身


// パレットリスト.
// ..各要素の中の色は、1つだけなら他の13色はその色を元に↓の2通りから自動取得、
var psx = 0;	// 0:彩度+明度を元に. 1:色相を循環で.

// 彩度+明度を元にするときの色. (複数設定する場合は、1つ1つの色を \n で区切る)
var pdefs = new Array(
	'#ffffff',
	'#ffe6e6','#ffece6','#fff3e6','#fff9e6','#ffffe6',
	'#f3ffe6','#e6fff3','#e6f3ff','#ffe6ff','#eeddbb',
'');	// ※空白だとその要素はスキップ.

// 色相で循環させるときの色. (複数設定する場合は、1つ1つの色を \n で区切る)
var pdefx = new Array(
	'#ffffff',
	'#ffe6e6','#ffcccc','#ff9999','#e6cccc','#e69999',
	'#cc9999','#cc6666','#996666','#993333','#660000',
'');	// ※空白だとその要素はスキップ.


// デフォルトのパレットカラー (一番最初にアプレットにでる色)
var pbase = '#000000\n#FFFFFF\n#B47575\n#888888\n#FA9696\n#C096C0\n#FFB6FF\n#8080FF\n#25C7C9\n#E7E58D\n#E7962D\n#99CB7B\n#FCECE2\n#F9DDCF';


// サンプルカラーの
  // 表示するパレットのカラー番号(この中にある番号だけ↓で書き出し)
var sams = new Array(0,2,4,6,8,10,12,1,3,5,7,9,11,13);	// 彩度+明度を元にするとき
var samx = new Array(0,1,2,3,4,5,6,7,8,9,10,11,12,13);	// 色相で循環させるとき

selfv[1] = '&nbsp;';	// フォント
selfv[2] = 'style="font-size:xx-small; background-color:$FONT;"';
	// フォントタグの中身(「$FONT」:16進法RGB色が代入、正確に適用される属性は↓の3つ)
	//  … color, style="color" style="background", style="background-color")

		// ※↓これより下は ">"(閉じタグ) も入れてください //

// パレットの選択ボタン(type=radio)タグの中身
selfv[3] = 'style="border-width:0;" title="デフォルトのパレット">';	// デフォルト色
selfv[4] = 'style="border-width:0;" title="ここのパレットを使う。\nチェックしてるときにさらに押すと、チェックが外れ、\n色相循環か、彩度+明度パレットに。(1番の色が基本色)">';	// 選択


// ボタン(type=button)タグの中身
selfv[5] = 'value="H" title="色相パレット (1番の色が基本色)">';	// 色相
selfv[6] = 'value="S" title="彩度パレット (1番の色が基本色)">';	// 彩度
selfv[7] = 'value="B" title="明度パレット (1番の色が基本色)">\n';	// 明度
selfv[8] = 'value="o" title="ここに今のパレットを保存">';	// セーブ
selfv[9] = 'value="x" title="ここのパレットをデフォルトに戻す"><br>\n';	// デフォルト

selfv[10] = 'value="H+" title="パレット全体の色相を＋">';	// 色相+
selfv[11] = 'value="H-" title="パレット全体の色相を－">';	// 色相-
selfv[12] = 'value="S+" title="パレット全体の彩度を＋">';	// 彩度+
selfv[13] = 'value="S-" title="パレット全体の彩度を－">';	// 彩度-
selfv[14] = 'value="B+" title="パレット全体の明度を＋">\n';	// 明度+
selfv[15] = 'value="B-" title="パレット全体の明度を－">\n';	// 明度-
selfv[16] = 'value="RGB+" title="パレット全体のRGBを＋"><br>\n';	// RGB+
selfv[17] = 'value="RGB-" title="パレット全体のRGBを－"><br>\n';	// RGB-


// グラデーションのとこ
selfv[18] = 'style="border-width:0;" title="2点を元にグラデーション (1番の色と14番の色)" checked>2';	// 2点
selfv[19] = 'style="border-width:0;" title="3点を元にグラデーション (1番、8番、14番の色)">3';	// 3点
selfv[20] = 'style="border-width:0;" title="4点を元にグラデーション (1、6、10、14番の色)">4<br>\n';	// 4点
selfv[21] = 'value="RGB" title="RGBでグラデーション">\n';	// RGB?
selfv[22] = 'value="+HSB" title="+HSBでグラデーション (色相＋方向)">\n';	// +HSB
selfv[23] = 'value="-HSB" title="-HSBでグラデーション (色相－方向)"><br>\n';	// -HSB


// 追加・削除
selfv[24] = 'value="+" title="パレットを追加します">';	// 追加
selfv[25] = 'value="-" title="選択中のパレットを削除します">\n';	// 削除


// セーブ・オートセーブ
selfv[26] = 'checked title="ここにチェックをつけておくと、色を変更するとき、\n　自動で保存パレットにパレット情報をセーブします。\n自動保存が適用されるのは、\n　チェックしてるパレットから他のパレットに移動したとき、\n　チェックしてるパレットのH/S/Bボタンを押したとき、\n　の2つのときです。サンプルの色も変わります。\nもちろん、ここにチェックしてなくても、\n　手動でセーブボタンを押せば、パレットに保存されます。">';	// 自動セーブ
selfv[27] = 'value="O" title="今の全体のパレットをクッキーに保存"><br>\n';	// セーブ


// デフォルトのパレットを 色相360°にするか、彩度++明度-- にするか
selfv[28] = 'style="border-width:0;" title="デフォルトのパレットは、色相で循環させる">H<sup>o</sup>';	// H°
selfv[29] = 'style="border-width:0;" title="デフォルトのパレットは、彩度＋、明度－でリスト">+S-B';	// +S-B
selfv[30] = 'value="X" title="全体のパレットをデフォルトに戻す"><br>\n';	// デフォルトに


// UPLOAD / DOWNLOAD
selfv[31] = 'value="" size=8 title="パレットデータ。\n・アップロードするときは、ここに貼り付けてください。\n・ダウンロードするときは、ここにデータが出力されます。\n　 ローカルのテキストにでも保存してください。\n※パレットデータは、\n pals = new Array(\'#FFFFFF\',\'#B47575\\n#888888\\n...\');\n　のように、JSの配列形式で書かれます。">\n';	// 
selfv[32] = 'value="↑" title="←からパレットデータをアップロード">';	// 
selfv[33] = 'value="↓" title="←←にパレットデータをダウンロード"><br>\n';	// 


// このパレットテーブルを囲んでる、ソースとかタグとか (formタグは消しちゃダメ)
// フォーム始まり
selfytag[0] = '<table class="ptable"><tr><form name="palepale"><td class="ptd" nowrap>\n<div align=right class="menu">Palette-Selfy</div>\n<div style="font-size:xx-small;">\n';

// フォームの間_1 (個別のパレット  ～  全体の HSB、RGB +- とか)
selfytag[1] = '<div style="text-align:right; padding:5;">\n';

// フォームの間_2 (全体の HSB、RGB +- とか  ～  グラデーション)
selfytag[2] = '</div>\n<div style="text-align:right; padding:0 5 5 5;"">\nGradation';

// フォームの間_3 (グラデーション ～ パレットの追加・削除ボタン)
selfytag[3] = '</div>\n<div style="text-align:right; padding:0 5 0 5;"">\nPalette';

// フォームの間_4 (パレットの追加・削除ボタン ～ セーブボタン)
selfytag[4] = '\nSave';

// フォームの間_5 (セーブボタン ～ Defaultは H++/+S-B どちらか)
selfytag[5] = '</div>\n<div style="text-align:right; padding:3 5 2 5;"">\nDefault';

// フォームの間_6 (Defaultは H++/+S-B どちらか ～ パレットのアップ/ダウンロード)
selfytag[6] = '</div>\n<div style="text-align:right; padding:0 5 0 5;">\nUpdata ';

// フォーム終わり
selfytag[7] = '</div>\n</div>\n</td></form></tr></table>\n';
//↑設定おわり ------------------------------------------------------


// 初期値 (いたるところで使う値)
var d = document;
var pon,pno;	// radioチェック中？  / チェックしたパレットNO.
var qon,qno,qmo;	// buttonプッシュ中？ / プッシュしたパレットNO.
var pals = new Array();	// color-palette
var inp = '<input type="button" ';	// input-button
var inr = '<input type="radio" ';	// input-button
var cname = 'selfy=';	// cookie-name
var psx_ch = new Array('','');	// h_sb-checked
var brwz=0;
if(d.all){ brwz=1; }else if(d.getElementById){ brwz=2; }


// -------------------------------------------------------------------------
// HSB→RGB 計算. 値は0～255.
function HSBtoRGB(h,s,v){
	var r,g,b;
	if(s==0){
		r=v; g=v; b=v;
	}else{
		var max,min,dif;
		h*=360/255;	//360°
		max=v;
		dif=v*s/255;	//s=(dif/max)*255
		min=v-dif;  //min=max-dif

		if(h<60){
			r=max;	b=min;	g=h*dif/60+min;
		}else if(h<120){
			g=max;	b=min;	r=-(h-120)*dif/60+min;
		}else if(h<180){
			g=max;	r=min;	b= (h-120)*dif/60+min;
		}else if(h<240){
			b=max;	r=min;	g=-(h-240)*dif/60+min;
		}else if(h<300){
			b=max;	g=min;	r= (h-240)*dif/60+min;
		}else if(h<=360){
			r=max;	g=min;	b=-(h-360)*dif/60+min;
		}else{r=0;g=0;b=0;}
	}
	return(new Array(r,g,b));
}


// RGB→HSB 計算. 値は0～255.
function RGBtoHSB(r,g,b){
	var max,min,dif,h,s,v;

	// max
	if(r>=g && r>=b){
		max=r;
	}else if(g>=b){
		max=g;
	}else{
		max=b;
	}

	// min
	if(r<=g && r<=b){
		min=r;
	}else if(g<=b){
		min=g;
	}else{
		min=b;
	}

	// 0,0,0
	if(max<=0){ return(new Array(0,0,0)); }

	// difference
	dif=max-min;

	//Hue:
	if(max>min){
		if(g==max){
			h=(b-r)/dif*60+120;
		}else if(b==max){
			h=(r-g)/dif*60+240;
		}else if(b>g){
			h=(g-b)/dif*60+360;
		}else{
			h=(g-b)/dif*60;
		}
		if(h<0){
			h=h+360;
		}
	}else{ h=0; }
	h*=255/360;

	//Saturation:
	s=(dif/max)*255;

	//Value:
	v=max;

	return(new Array(h,s,v));
}


// RGB16→RGB10 表記. 値は 000000～ffffff
function to10rgb(str){
	var ns = new Array();
	str = str.replace(/[^0-9a-fA-F]/g,'');
	for(var i=0; i<=2; i++){
		ns[i] = str.substr(i*2,2);
		if(!ns[i]){ ns[i]='00'; }
		ns[i] = Number(parseInt(ns[i],16).toString(10));
	}
	return(ns);
}


// 10→16進法二桁
function format16(n){
	n = Number(n).toString(16);
	if(n.length<2){ n='0'+n; }
	return(n);
}




// -------------------------------------------------------------------------
// パレットに (※ q=1:アプレットパレットに出力しない. lst=1:最初のとき
function rady(p,q,lst){
	var d = document;
	var df = d.forms.palepale;

	// デフォルトパレット
	if(!p&&p!=0){ pon=0; pno=''; d.paintbbs.setColors(pbase); return; }

	var ps = pals[p].split('\n');
	var n = pnum;
	if(!q && df.num.value){ n = Number(df.num.value); }
	if(!q && pon==1 && pno!=p){ poncheck(); }

	// 揃ってるならすぐ返す
	if((pon!=1 || pno!=p) && ps.length==14){
		if(!q){ pon=1; pno=p; }
		if(q!=1 && pals[p]){ d.paintbbs.setColors(pals[p]); } return;
	}

	// checkしてるなら
	if(pon==1 && pno==p){
		var pget = String(d.paintbbs.getColors());
//		if(pget==pals[p]){ return; }
		var cs = pget.split('\n');
		ps[0] = cs[0];  ps[1] = '';
	}
	// 欠けているなら
	var cs = new Array();

	var psy=0;	// H°/ +S-B
	psy = check_h_sb(lst);

	if(psy==1){ cs = rh_list(p,n); }// H°リスト
	else{ cs = sb_list(p,n); }	// +S-B リスト

	if(q){	// 初期設定時
		pals[p] = String(cs.join('\n'));
	}
	if(q!=1){	// 一般
		if(pon==1 && pno==p){ checkout(); }
		else{ pon=1; pno=p; }
//		pals[p] = String(cs.join('\n'));
		d.paintbbs.setColors(String(cs.join('\n')));
	}
}


// H°リスト
function rh_list(p,n){
	var ps = pals[p].split('\n');
	var rgb = to10rgb(ps[0]);	//→RGB
	var hsv = RGBtoHSB(rgb[0],rgb[1],rgb[2]);	//→HSB
	var cs = new Array(ps[0],ps[1]);
	if(!cs[0]){ cs[0]='#ffffff'; }
	if(hsv[1]!=0 && !cs[13]){ cs[13]='#ffffff'; }

	for (var i=1; i<13; i++){
		if(ps[i] && (pon!=1 || pno!=p)){ cs[i]=ps[i]; continue; }	//ある
		var x,y,z;
		if(hsv[1]==0){	//白黒
			x = hsv[0];
			y = 0;
			if(i%2==0){ z = 255-i*n; }else{ z = 0+(i-1)*n; }
		}else if(i>=12){
			x = hsv[0];
			y = 0;
			z = 255-hsv[1];
		}else{
			x = hsv[0] + i*255/12;
			y = hsv[1];
			z = hsv[2];
		}
		while(x<0){ x+=255; }	if(y<0){ y=0; }	if(z<0){ z=0; }	//↓0
		while(x>255){ x-=255; }	if(y>255){ y=255; }	if(z>255){ z=255; }	//↑255
//		for (var j=0; j<=2; j++){ hsv[j] = Math.round(hsv[j]); }
		rgb = HSBtoRGB(x,y,z);
		for (var j=0; j<=2; j++){ rgb[j] = Math.round(rgb[j]); }
		cs[i] = '#'+format16(rgb[0])+format16(rgb[1])+format16(rgb[2]);
	}
	return(cs);
}


// +S-B リスト
function sb_list(p,n){
	var ps = pals[p].split('\n');
	var rgb = to10rgb(ps[0]);	//→RGB
	var hsv = RGBtoHSB(rgb[0],rgb[1],rgb[2]);	//→HSB
	var cs = new Array(ps[0],ps[1]);
	if(!cs[0]){ cs[0]='#ffffff'; }
	if(hsv[1]==0 && !cs[1]){ cs[1]='#000000'; }
	else if(!cs[1]){ cs[1]='#ffffff'; }

	for (var i=2; i<14; i++){
		if(ps[i] && (pon!=1 || pno!=p)){ cs[i]=ps[i]; continue; }	//ある
		var y,z;
		if(hsv[1]==0){	//白黒
			y = 0;
			if(i%2==0){ z = 255-i*n; }else{ z = 0+(i-1)*n; }
		}else{
			if(i%2==0){	//左
				y = hsv[1]+i*n;
				z = hsv[2];
			}else{	//右
				y = hsv[1]+(i-1)*n;
				z = hsv[2]-(i-1)*n;
			}
		}
		while(z<0){ z+=255; }	while(y<0){ y+=255; }	//↓0
		while(z>255){ z-=255; }	while(y>255){ y-=255; }	//↑255
//		for (var j=0; j<=2; j++){ hsv[j] = Math.round(hsv[j]); }
		rgb = HSBtoRGB(hsv[0],y,z);
		for (var j=0; j<=2; j++){ rgb[j] = Math.round(rgb[j]); }
		cs[i] = '#'+format16(rgb[0])+format16(rgb[1])+format16(rgb[2]);
	}
	return(cs);
}


// 個別でH/S/Bをリストアップ
function onplus(p,m){
	var d = document;
	var df = d.forms.palepale;
	var n = Number(df.num.value);	//+-
	if(pon==1 && pno==p){ poncheck(); }

	// 連続のとき
	if(m>0 && n*(qon+1)>38){ qon=0; }
	if(qno==p && qmo==m && qon>=1){ qon++; n*=(qon+1)/2; }
	else{ qno=p; qmo=m; qon=1; }

	var ps = pals[p].split('\n');
	var rgb = to10rgb(ps[0]);	//→RGB
	var hsv = RGBtoHSB(rgb[0],rgb[1],rgb[2]);	//→HSB
	var cs = new Array();
	if(m==2){ n*=-1; }
	for (var i=0; i<14; i++){
		var z;
		if(m==0){ z = hsv[m]+((i%2)*2-1)*Math.round(Math.floor(i/2)*(n)); }
		else{ z = hsv[m]+i*n; }
		while(z<0){ z+=255; }	//↓0
		while(z>255){ z-=255; }	//↑255
//		for (var j=0; j<=2; j++){ hsv[j] = Math.round(hsv[j]); }
		if(m==1){ rgb = HSBtoRGB(hsv[0],z,hsv[2]); }	//→HSB
		else if(m==2){ rgb = HSBtoRGB(hsv[0],hsv[1],z); }
		else{ rgb = HSBtoRGB(z,hsv[1],hsv[2]); }	//→HSB
		for (var j=0; j<=2; j++){ rgb[j] = Math.round(rgb[j]); }
		cs[i] = '#'+format16(rgb[0])+format16(rgb[1])+format16(rgb[2]);
	}
	checkout(1);
	d.paintbbs.setColors(String(cs.join('\n')));
}


// 全体のH/S/Bをプラスマイナス
function alplus(m,n){
	var d = document;
	var cs = String(d.paintbbs.getColors()).split('\n');
	n *= Number(d.forms.palepale.num.value);	//+-
	poncheck();

	for (var i=0; i<cs.length; i++){
		var rgb = to10rgb(cs[i]);	//→RGB
		var hsv = RGBtoHSB(rgb[0],rgb[1],rgb[2]);	//→HSB
		//↑明度255のとき彩度減
		if(m==2 && n>0 && hsv[2]>=255){
			hsv[1] -= n;
			if(hsv[1]<0){ hsv[1]=0; }else if(hsv[1]>255){ hsv[1]=255; }	//↓0 or ↑255
		}
		hsv[m] += n;
		//↓0 ↑255
		if(m==0){
			if(hsv[0]<0){ hsv[0]+=255; }else if(hsv[0]>255){ hsv[0]-=255; }
		}else{
			if(hsv[m]<0){ hsv[m]=0; }else if(hsv[m]>255){ hsv[m]=255; }
		}
//		for (var j=0; j<=2; j++){ hsv[j] = Math.round(hsv[j]); }
		rgb = HSBtoRGB(hsv[0],hsv[1],hsv[2]);	//→HSB
		for (var j=0; j<=2; j++){ rgb[j] = Math.round(rgb[j]); }
		cs[i] = '#'+format16(rgb[0])+format16(rgb[1])+format16(rgb[2]);
	}
	checkout();
	d.paintbbs.setColors(String(cs.join('\n')));
}


// 全体のRGBをプラスマイナス
function alrgb(n){
	var d = document;
	var cs = String(d.paintbbs.getColors()).split('\n');
	n *= Number(d.forms.palepale.num.value);	//+-
	poncheck();

	for (var i=0; i<cs.length; i++){
		var rgb = to10rgb(cs[i]);	//→RGB
		for (var j=0; j<=2; j++){
			rgb[j] += n;
			rgb[j] = Math.round(rgb[j]);
			if(rgb[j]<0){ rgb[j]=0; }	//↓0
			if(rgb[j]>255){ rgb[j]=255; }	//↑255
		}
		cs[i] = '#'+format16(rgb[0])+format16(rgb[1])+format16(rgb[2]);
	}
	checkout();
	d.paintbbs.setColors(String(cs.join('\n')));
}


// グラデーション
function grady(m){
	var d = document;
	var df = d.forms.palepale;
	var n = 2;
	if(df.gradc){
		for(var j=0; j<df.gradc.length; j++){
			if(df.gradc[j].checked == true){ n = Number(df.gradc[j].value);  break; }
		}
	}
	var cs = String(d.paintbbs.getColors()).split('\n');
	var gs = new Array(1,13);
	if(n==3){ gs = new Array(1,7,13); }
	else if(n==4){ gs = new Array(1,5,9,13); }
	poncheck();
	cs[1] = cs[0];

	// 2～4色
	for (var i=0; i<gs.length-1; i++){
		var p=gs[i]; var q=gs[(i+1)];
		var rgbp = to10rgb(cs[p]);	//→RGB
		var rgbq = to10rgb(cs[q]);	//→RGB2
		// HSB
		var hsvp = new Array();
		var hsvq = new Array();
		if(m==1 || m==-1){
			hsvp = RGBtoHSB(rgbp[0],rgbp[1],rgbp[2]);	//→HSB
			hsvq = RGBtoHSB(rgbq[0],rgbq[1],rgbq[2]);	//→HSB
		}
		// パレットの色
		for (var k=p+1; k<q; k++){
			var rgb = new Array();
			// HSB
			if(m==1 || m==-1){
				var hsv = new Array();
				for (var j=0; j<=2; j++){ // RGB
					var sa = (hsvp[j]-hsvq[j])/(q-p);
					if(j==0){	// H
						if(m*hsvp[j]>m*hsvq[j]){ sa = Math.abs(sa) - 255/(q-p); }
						hsv[0] = hsvp[0] + m*Math.abs(sa)*(k-p);
						if(hsv[0]<0){ hsv[0]+=255; }else if(hsv[0]>255){ hsv[0]-=255; }
					}else{	// S,B
						hsv[j] = hsvp[j] - sa*(k-p);
						if(hsv[j]<0){ hsv[j]=0; }else if(hsv[j]>255){ hsv[j]=255; }
					}
				}
				rgb = HSBtoRGB(hsv[0],hsv[1],hsv[2]);	//→HSB
				for (var j=0; j<=2; j++){ rgb[j] = Math.round(rgb[j]); }
			// RGB
			}else{
				for (var j=0; j<=2; j++){ // RGB
					var sa = (rgbp[j]-rgbq[j])/(q-p);
					rgb[j] = Math.round(rgbp[j] - sa*(k-p));
					if(rgb[j]<0){ rgb[j]=0; }else if(rgb[j]>255){ rgb[j]=255; }	//↑↓
				}
			}
			cs[k] = '#'+format16(rgb[0])+format16(rgb[1])+format16(rgb[2]);
		}
	}
	cs[0]=cs[1]; cs[1]='#ffffff';
	checkout();
	d.paintbbs.setColors(String(cs.join('\n')));
}


// -------------------------------------------------------------------------
// パレットのサンプルカラー
function csamp(p,pz,lst){
	var ss='';
	var ps = pz.split('\n');
	var slong = sams.length;
	var psy = check_h_sb(lst);  if(psy==1){ slong = samx.length; }
	// color-sample
	for (var i=0; i<slong; i++){
	// color-title
		var k,cl='',rgb='',hsv='',ctl='';
		if(psy==1){ k=samx[i]; }else{ k=sams[i]; }
		if(ps[k]){
			rgb = to10rgb(ps[k]);	//→RGB
			hsv = RGBtoHSB(rgb[0],rgb[1],rgb[2]);	//→HSB
			for (var j=0; j<=2; j++){ hsv[j] = Math.round(hsv[j]); }
			ctl  = 'HSB: '+hsv[0]+','+hsv[1]+','+hsv[2]+'\n';
		    ctl += 'RGB: '+rgb[0]+','+rgb[1]+','+rgb[2]+'\nRGB16: '+ps[k];
		}
		if(selfv[2]) cl=selfv[2].replace(/\$FONT/i,ps[k]);
		if(selfv[1]) ss += '<font id="font_'+p+'_'+k+'" '+cl+' title="'+ctl+'">'+selfv[1]+'</font>';
	}
	return ss;
}


// パレットのリスト
function palette_list(lst){
	var d = document;
	var ds = '';
	for (var p=0; p<pals.length; p++){
		if(!pals[p]){ continue; }
		var samw = csamp(p,pals[p],lst); //サンプル

		// element
		if(selfv[4]) ds+=inr+'name="rad" value="'+p+'" onclick="rady('+p+')" '+selfv[4]+samw+'\n';
//		ds+='<font color="'+ps[0]+'" id="font_'+p+'" title="'+ctl+'">'+samw+'</font>';
		if(selfv[5]) ds+=inp+'onclick="onplus('+p+',0)" '+selfv[5];
		if(selfv[6]) ds+=inp+'onclick="onplus('+p+',1)" '+selfv[6];
		if(selfv[7]) ds+=inp+'onclick="onplus('+p+',2)" '+selfv[7];
		if(selfv[8]) ds+=inp+'onclick="savy('+p+')" '+selfv[8];
		if(selfv[9]) ds+=inp+'onclick="defy('+p+')" '+selfv[9];
	}
	return ds;
}


// チェックをつける、フォントカラーのサンプルを変更
function checkin(p,not){
	qno=''; qmo=''; qon=0;
	if(!pals[p]){ return; }
	var d = document;
	// font-color
	var ps = pals[p].split('\n');
	var slong = sams.length;
	var psy = check_h_sb();  if(psy==1){ slong = samx.length; }
	// color-sample
	for (var i=0; i<slong; i++){
	// color-title
		var k,rgb='',hsv='',ctl='';
		if(psy==1){ k=samx[i]; }else{ k=sams[i]; }
		if(ps[k]){
			rgb = to10rgb(ps[k]);	//→RGB
			hsv = RGBtoHSB(rgb[0],rgb[1],rgb[2]);	//→HSB
			for (var j=0; j<=2; j++){ hsv[j] = Math.round(hsv[j]); }
			ctl  = 'HSB: '+hsv[0]+','+hsv[1]+','+hsv[2]+'\n';
		    ctl += 'RGB: '+rgb[0]+','+rgb[1]+','+rgb[2]+'\nRGB16: '+ps[k];
		}
		// replace
		var ds;
		if(brwz==1){ ds = d.all('font_'+p+'_'+k); }
		else if(brwz==2){ ds = d.getElementById('font_'+p+'_'+k); }
		if(ds){
			if(ds.style.background){ ds.style.background = ps[k]; }
			if(ds.style.backgroundColor){ ds.style.backgroundColor = ps[k]; }
			if(ds.style.color){ ds.style.color = ps[k]; }
			if(ds.color){ ds.color = ps[k]; }
		}
	}

	// check
	if(not!=1){
		var df = d.forms.palepale;
		for(var j=0; j<df.rad.length; j++){
			if(df.rad[j].value == p){
				df.rad[j].checked = true;  break; }
		}
	}
}


// checkを外す
function checkout(q){
	pon=0; pno='';
	if(q!=1){ qno=''; qmo=''; qon=0; }
	var df = document.forms.palepale;
	for(var j=0; j<df.rad.length; j++){
		if(df.rad[j].checked == true){
			 df.rad[j].checked = false;  break; }
	}
}


// 以前のパレットを自動保存
function poncheck(not){
	var d = document;
	var df = document.forms.palepale;
	if(df.autosave&&df.autosave.checked==false){ return; }
	else if(pon==1){
		var pget = String(d.paintbbs.getColors());
		if(pals[pno] != pget){
			pals[pno] = pget;
			checkin(pno,1);
			if(not!=1){ pcookset(1); }
		}
	}
}


// パレットをセーブ
function savy(p){
	var d = document;
	pals[p] = String(d.paintbbs.getColors());
	checkin(p);
	pcookset(1);
	pon=1; pno=p;
}


// パレットをデフォルトに
function defy(p){
	checkout();
	var q = pdefs[p];
	var df = document.forms.palepale;
	if(check_h_sb()==1){ q = pdefx[p]; }
	if(q){
		pals[p] = q;
		rady(p,2);
		checkin(p);
	}else{ minsy(p); }
}


// パレット追加
function plusy(){
	var d = document;
	if(brwz==1 || brwz==2){
		var p=pals.length;
		var pz = String(d.paintbbs.getColors());
		if(pz){ pals[p] = pz; }
		else{
			pals[p] = '#'+Number(d.paintbbs.getInfo().m.iColor).toString(16);
			rady(p,1);
		}
	}
	if(brwz==1 && d.all('palelist').innerHTML){
		d.all('palelist').innerHTML = palette_list();
		checkin(p);
	}else if(brwz==2 && d.getElementById('palelist').innerHTML){
		d.getElementById('palelist').innerHTML = palette_list();
		checkin(p);
	}
}


// パレット削除
function minsy(p){
	var d = document;
	var df = d.forms.palepale;
	if(!p&&p!=0){
		for(var j=0; j<=df.rad.length; j++){
			if(df.rad[j] && df.rad[j].checked==true){p=Number(df.rad[j].value); break; }
		}
	}
	if((!p&&p!=0)||p<0){ return; }
	pals[p] = '';
	var plong = pdefs.length;
	if(check_h_sb()==1){ plong = pdefx.length; }
	if(p>=plong){
		var k=0;
		var pds = new Array(); pds = pals;
		pals = new Array(); 
		for(var j=0; j<pds.length; j++){
			if(p!=j && pds[j]){ pals[k] = pds[j]; k++; }
		}
	}

	if(brwz==1 && d.all('palelist').innerHTML){
		d.all('palelist').innerHTML = palette_list();
	}else if(brwz==2 && d.getElementById('palelist').innerHTML){
		d.getElementById('palelist').innerHTML = palette_list();
	}
	checkout();
}


// パレットデフォルト
function def_list(){
	var okd = confirm("全体のパレットをデフォルトに戻します。\nよろしいですか？");
	if(!okd){ return; }
	var d = document;
	var df = d.forms.palepale;
	pals = new Array();
	var psy = 0;
	var plong = pdefs.length;
	if(check_h_sb()==1){ psy=1;  plong = pdefx.length; }
	for (var p=0; p<plong; p++){
		if(psy==1){ pals[p]=pdefx[p]; }else{ pals[p]=pdefs[p]; }
	}
	for (var p=0; p<pals.length; p++){ if(pals[p]){ rady(p,1); } }

	if(brwz==1 && d.all('palelist').innerHTML){
		d.all('palelist').innerHTML = palette_list();
	}else if(brwz==2 && d.getElementById('palelist').innerHTML){
		d.getElementById('palelist').innerHTML = palette_list();
	}else{
		for (var p=0; p<pals.length; p++){
			if(pals[p]){ checkin(p,1); }
		}
	}
}


// デフォルト h_sb のフォームのチェック. H°にチェックがついてるなら1
function check_h_sb(lst){
	var ch = 0;
	var df = document.forms.palepale;
	if(lst!=1 && df && df.h_sb){
		for (var i=0; i<df.h_sb.length; i++){
			if(df.h_sb[i].value==1 && df.h_sb[i].checked==true){ ch=1; break; }
		}
	}else{ ch=psx; }
	return ch;
}


// パレットデータ アップロード
function pupload(){
	var d = document;
	var df = d.forms.palepale;
	var qs = new Array();
	var palx='';
	if(df.palz){ palx = df.palz.value; }
	if(!palx){ return; }
	pals = new Array();
	if(eval(palx)){}
	else{
		var px = palx.split(/\(|\)/);
		var ps = px[1].split(',');
		for (var p=0; p<ps.length; p++){
			var q=ps[p].replace(/[^0-9a-fA-F]/g,'');  pals[p] = q;
		}
	}

	for (var p=0; p<pals.length; p++){ if(pals[p]){ rady(p,1); } }

	if(brwz==1 && d.all('palelist').innerHTML){
		d.all('palelist').innerHTML = palette_list();
	}else if(brwz==2 && d.getElementById('palelist').innerHTML){
		d.getElementById('palelist').innerHTML = palette_list();
	}else{
		for (var p=0; p<pals.length; p++){
			if(pals[p]){ checkin(p,1); }
		}
	}
}


// パレットデータ ダウンロード
function pdownload(){
	var d = document;
	var df = d.forms.palepale;
	var qs = new Array();
	for (var p=0; p<pals.length; p++){
		qs[p] = "\'"+pals[p].replace(/\n/g,'\\n')+"\'";
	}
	var palx = 'pals = new Array(\n' + qs.join('\,\n') + '\n);';
	if(df.palz){ df.palz.value = palx; }
}


// 全体のパレット情報をクッキーにセーブ
function pcookset(o){
	var df = document.forms.palepale;
	if(o&&df.autosave&&df.autosave.checked==false){ return; }
	var exp=new Date();
	exp.setTime(exp.getTime()+1000*86400*60);
	var cs = new Array();
	for(var i=0; i<pals.length; i++){
		cs[i] = escape(pals[i].replace(/\n/g,'_'));
	}
	var cooki = '';
	if(df.num){ cooki += df.num.value; }
	cooki += '_'+check_h_sb()+'_%00';
	cooki += cs.join('%00');
	document.cookie = cname + cooki + "; expires=" + exp.toGMTString();
}


// 全体のパレット情報をクッキーからロード
function pcookget(){
	var cooks = document.cookie.split("; ");
	var cooki = '';
	for (var i=0; i<cooks.length; i++){
		if (cooks[i].substr(0,cname.length) == cname){
			cooki = cooks[i].substr(cname.length,cooks[i].length);
			break;
		}
	}
	if(cooki){
		var cs = cooki.split('%00');
		pals = new Array();
		for(var i=0; i<cs.length-1; i++){
			pals[i] = unescape(cs[(i+1)]).replace(/\_/g,"\n");
		}
		if(cs[0]){
			var ps = cs[0].split('_');
			if(ps[0]){ pnum = ps[0]; }
			if(ps[1]){ psx = ps[1]; }else if(!ps[1]&&ps[1]==0){ psx=0; }
		}
	}
}


// 増減する数を増やしたり減らしたり
function num_plus(n){
	var df = document.forms.palepale;
	var m = Number(df.num.value); var l=n;
	n *= Math.abs(Math.round(m/10))+1;  if(n==0){ n=l; }
	df.num.value = m+n;
}


// トーンセレクトの値を±
function tone_plus(n){
	var df = document.forms.palepale;
	var m = Number(df.tone.value);
	if(m>0){ n = Math.floor(m/10 + n)*10; }
	if(n<0){ n=0; }else if(n<5){ n=5; }else if(n>100){ n=100; }
	df.tone.value = n;
	tone_sel(n);
}


// トーンセレクト
function tone_sel(t){
	var dp=document.paintbbs;
	t = Number(t);
	if(t==0){ dp.getInfo().m.iTT = 0; }
	else{ dp.getInfo().m.iTT = Math.floor(t/10)+1; }
}


// -------------------------------------------------------------------------
// document.write
function palette_selfy(){
	var d = document;
	var df = document.forms.palepale;
	var pzs=palette_selfy.arguments;	//パレット指定があったとき

	// browzer
	if(brwz!=1 && brwz!=2){ return; }

	// パレットとパレットクッキー
	var plong = pdefs.length;
	if(psx==1){ plong = pdefx.length; }
	for (var p=0; p<plong; p++){
		if(psx==1){ pals[p]=pdefx[p]; }else{ pals[p]=pdefs[p]; }
		if(pzs && pzs.length>=1){	var ok=0;	//？
			for (var q=0; q<pzs.length; q++){ if(p==pzs[q]){ ok=1; break; } }
			if(ok!=1){ pals[p]=''; }
		}
	}
	pcookget();	// cookie-get
	psx_ch[psx] = 'checked ';	
	for (var p=0; p<pals.length; p++){ if(pals[p]){ rady(p,1,1); } }

	// basic
	d.write(selfytag[0]);
	if(selfv[3]) d.write(inr+'name="rad" value="-1" onclick="rady()" '+selfv[3]);
	if(pbase) d.write(csamp(-1,pbase,1));

	// +-する数
	if(selfv[0]){
		d.write('\n<small>&nbsp;</small>+-');
		d.write('<input type="text" name="num" value="'+pnum+'" '+selfv[0]);
		d.write(inp+'value="+" onclick="num_plus(1)">');
		d.write(inp+'value="-" onclick="num_plus(-1)">\n');
	}
	// パレットリスト
	if(pdefs||pdefx) d.write('<div id="palelist">\n'+palette_list(1)+'</div>\n');

	// 全体の HSB、RGB +-
	if(selfytag[1]) d.write(selfytag[1]);
	if(selfv[10]) d.write(inp+'onclick="alplus(0,1)" ' +selfv[10]);
	if(selfv[12]) d.write(inp+'onclick="alplus(1,1)" ' +selfv[12]);
	if(selfv[14]) d.write(inp+'onclick="alplus(2,1)" ' +selfv[14]);
	if(selfv[16]) d.write(inp+'onclick="alrgb(1)" '    +selfv[16]);
	if(selfv[11]) d.write(inp+'onclick="alplus(0,-1)" '+selfv[11]);
	if(selfv[13]) d.write(inp+'onclick="alplus(1,-1)" '+selfv[13]);
	if(selfv[15]) d.write(inp+'onclick="alplus(2,-1)" '+selfv[15]);
	if(selfv[17]) d.write(inp+'onclick="alrgb(-1)" '   +selfv[17]);

	// トーンセレクト
	if(selfv[0]){
		d.write('Tone <select name="tone" onchange="tone_sel(this.value)">');
		for (var i=0; i<=100; i+=5){
			d.write('<option value="'+i+'">'+i+'%</option>\n'); if(i>=10){i+=5;}
		}
		d.write('</select>');
		d.write(inp+'value="+" onclick="tone_plus(1)">');
		d.write(inp+'value="-" onclick="tone_plus(-1)">\n');
	}

	// GRADATION
	if(selfytag[2]) d.write(selfytag[2]);
	if(selfv[18]) d.write(inr+'name="gradc" value="2" '+selfv[18]);	//18
	if(selfv[19]) d.write(inr+'name="gradc" value="3" '+selfv[19]);	//19
	if(selfv[20]) d.write(inr+'name="gradc" value="4" '+selfv[20]);	//20
	if(selfv[21]) d.write(inp+'onclick="grady(0)" '    +selfv[21]);	//21
	if(selfv[22]) d.write(inp+'onclick="grady(1)" '    +selfv[22]);	//22
	if(selfv[23]) d.write(inp+'onclick="grady(-1)" '   +selfv[23]);	//23

	// 追加・削除
	if(selfytag[3]) d.write(selfytag[3]);
	if(selfv[24]) d.write(inp+'onclick="plusy()" '     +selfv[24]);	//24
	if(selfv[25]) d.write(inp+'onclick="minsy()" '     +selfv[25]);	//25

	// セーブ・オートセーブ
	if(selfytag[4]) d.write(selfytag[4]);
	if(selfv[26]) d.write('<input type="checkbox" name="autosave" value="1" '+selfv[26]);	//26
	if(selfv[27]) d.write(inp+'onclick="pcookset()" '  +selfv[27]);	//27

	// デフォルト
	if(selfytag[5]) d.write(selfytag[5]);
	if(selfv[28]) d.write(inr+'name="h_sb" value="1" ' +psx_ch[1]+selfv[28]);	//28
	if(selfv[29]) d.write(inr+'name="h_sb" value="0" ' +psx_ch[0]+selfv[29]);	//29
	if(selfv[30]) d.write(inp+'onclick="def_list()" '  +selfv[30]);	//30

	// UPLOAD / DOWNLOAD
	if(selfytag[6]) d.write(selfytag[6]);
	if(selfv[31]) d.write('<input type="text" name="palz" '+selfv[31]);	//31
	if(selfv[32]) d.write(inp+'onclick="pupload()" '   +selfv[32]);	//32
	if(selfv[33]) d.write(inp+'onclick="pdownload()" ' +selfv[33]);	//33

	// /FORM
	if(selfytag[7]) d.write(selfytag[7]);
}
