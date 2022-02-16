var $ = require("jquery");
var Picture = require("./includes/picture_functions");
require("../views/blocks/popup/popup")
import Validator from "./includes/validator"
require("./libs/jq_input_mas")
require("./includes/form.js")



$(document).ready(function(){


	/*  header */
		require("../views/blocks/menu/menu_bottom/menu-cat/menu_big")
		require("../views/includes/sections/header/header")
		require("../views/blocks/menu/menu_top/btn/menu-btn")
	/*END header*/

	/* profile tab */

	$(".profile-tab").on("click",function(){
		var data_class = $(this).data("tab");
		$(".profile-tab").removeClass("active");
		$(this).addClass("active");
		$(".tab_fn").fadeOut("slow", function(){
			$(`.${data_class}`).fadeIn("slow")
		})

	});

	/* END profile tab */


	Picture.lazy_load_launch();
	Picture.background_is_picture_launch();

	require("./includes/global_auth");
    require("./includes/profile_update");
    require("./includes/global_callback");
    require("./includes/global");


});

