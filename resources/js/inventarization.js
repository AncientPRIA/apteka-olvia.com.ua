
//

$(".product_item_img, .btn_product_link, .sell-home-grid-item, .recommendations_item, .menu-aside-sub-item__nav a, .product_item_img, .menu-link:nth-child(2), .menu_list_left .menu_item:nth-child(2) a, .menu-main a, .modal_basket_empty_link, .btn_all_product, .smart-search a").on("click", function (e) {
    e.preventDefault();
    show_inventarization();
    return;
})

function show_inventarization() {
    console.log("[INV] trigger");
    popup_show({cls:'Inventarization', scrollOff:'body'});
}