// Let's test this function
function isEven(val) {
    return val % 2 === 0;
}

function getCookie(cname) {
		var name = cname + "=";
		var ca = document.cookie.split(';');
		for(var i=0; i<ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1);
			if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
		}
		return "";
	}
 
QUnit.test('isEven()', function() {
    ok(isEven(0), 'Zero is an even number');
    ok(isEven(2), 'So is two');
    ok(isEven(-4), 'So is negative four');
    ok(!isEven(1), 'One is not an even number');
    ok(!isEven(-7), 'Neither does negative seven');
 
    // Fails
    ok(isEven(3), 'Three is an even number');
})

QUnit.test('getCookie()', function(assert) {
	assert.equal( getCookie("pagemode"), "Single", 'Cookie excists and is Single');
	assert.equal( getCookie("pagemode"), "Stream", 'Cookie excists and is Strem');
	assert.equal( getCookie("pagemode"), "", 'Cookie does not excist');
})