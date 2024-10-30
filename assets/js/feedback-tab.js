// Add config data in current window object 
function configChd() {
  !window.chdData?window.chdData=[]:null;
  window.chdData.push(arguments);
}

var Helprace = {
    
	encrypt : function (a, d, b) {
		for (var c = "", e = 0; e < a.length; e++) var f = this.fixedCharCodeAt(a, e) + this.fixedCharCodeAt(d, e % d.length) * (b ? -1 : 1),
			c = c + this.fixedFromCharCode(f);
		return c;
    },
	
	fixedFromCharCode : function(a) {
	  return 65535 < a ? (a -= 65536, String.fromCharCode(55296 + (a >> 10), 56320 + (a & 1023))) : String.fromCharCode(a)
    },
	
	fixedCharCodeAt : function( a, d ){
		var b = a.charCodeAt(d);
		if (55296 <= b && 56319 >= b) {
			var c = b, b = a.charCodeAt(d + 1);
			return 1024 * (c - 55296) + (b - 56320) + 65536;
		}
		return 56320 <= b && 57343 >= b ? (c = a.charCodeAt(d - 1), 1024 * (c - 55296) + (b - 56320) + 65536) : b
	},
	
	base64_encode: function (data) {
		var o1, o2, o3, h1, h2, h3, h4, bits, r,
			i = 0,
			b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
			ac = 0,
			enc = "",
			tmp_arr = [];

		if (!data) {
			return data;
		}

		do { // pack three octets into four hexets
			o1 = data.charCodeAt(i++);
			o2 = data.charCodeAt(i++);
			o3 = data.charCodeAt(i++);

			bits = o1 << 16 | o2 << 8 | o3;

			h1 = bits >> 18 & 0x3f;
			h2 = bits >> 12 & 0x3f;
			h3 = bits >> 6 & 0x3f;
			h4 = bits & 0x3f;

			// use hexets to index into b64, and append result to encoded string
			tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);

		} while (i < data.length);

		enc = tmp_arr.join('');

		r = data.length % 3;

		return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);
    },
	// Generate helprace account key
	accountKey : function( account_name, keyword_content, keyword_url ){
	  return this.base64_encode( account_name + "|" + 
			 this.encrypt( keyword_content + "|" + keyword_url, account_name)
		);	
	}

};



