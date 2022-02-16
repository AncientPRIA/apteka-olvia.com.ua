var $ = require("jquery");
// var jQueryBridget = require("jquery-bridget");
// var Flickity = require("flickity");
var Picture = require("./includes/picture_functions");

$(document).ready(function(){
	Picture.lazy_load_launch();
	Picture.background_is_picture_launch();

    require("./includes/global_auth");
    require("./includes/global_subscribe");
    require("./includes/global_callback");
    require("./includes/global");
});

