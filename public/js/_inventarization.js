(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["js/_inventarization"],{

/***/ "./resources/js/inventarization.js":
/*!*****************************************!*\
  !*** ./resources/js/inventarization.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("//\n$(\".product_item_img, .btn_product_link, .sell-home-grid-item, .recommendations_item, .menu-aside-sub-item__nav a, .product_item_img, .menu-link:nth-child(2), .menu_list_left .menu_item:nth-child(2) a, .menu-main a, .modal_basket_empty_link, .btn_all_product, .smart-search a\").on(\"click\", function (e) {\n  e.preventDefault();\n  show_inventarization();\n  return;\n});\n\nfunction show_inventarization() {\n  console.log(\"[INV] trigger\");\n  popup_show({\n    cls: 'Inventarization',\n    scrollOff: 'body'\n  });\n}\n\n//# sourceURL=webpack:///./resources/js/inventarization.js?");

/***/ })

},[["./resources/js/inventarization.js","runtime~js/_inventarization"]]]);