var $ = require("jquery");
require("../views/blocks/popup/popup")
require("./libs/jq_input_mas")
require("./includes/form.js")

var Picture = require("./includes/picture_functions");
const { detect } = require('detect-browser');
const browser = detect();
$(document).ready(function(){

	if (browser && browser.name == "ios") {
		let version = browser.version.split(".");
		if(parseInt(version) <= 10 ){
			$(".product_item").addClass("product_item_ios-10");
			// alert(browser.name +" "+browser.version+" "+browser.os);
		}

	}


	/*  header */
	require("../views/blocks/menu/menu_bottom/menu-cat/menu_big")
	require("../views/includes/sections/header/header")
	require("../views/blocks/menu/menu_top/btn/menu-btn")
	/*END header*/


	Picture.lazy_load_launch();
	Picture.background_is_picture_launch();


	require("./includes/global_auth");
	require("./includes/global_subscribe");
    require("./includes/global_callback");
    require("./includes/global");

});
