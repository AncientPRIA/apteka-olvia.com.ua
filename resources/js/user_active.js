const $ = require("jquery");
const Picture = require("./includes/picture_functions");
import Favorite from "./includes/favorit"

$(document).ready(function(){

	//Favorite.
	Favorite.changeFavorit();
	Favorite.delFavoritItem();
	Favorite.favoritCheckActive(function(){
		console.log("fav init");
	});

	Picture.lazy_load_launch();
	Picture.background_is_picture_launch();
});

