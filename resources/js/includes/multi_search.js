
export default class MultiSearch {

    // Constants
    AJAX_BASE_URL = window.baseUrl;

    ajax = {
        url: this.AJAX_BASE_URL += "/api/multi_search",
        request: null,
    };

    data = {
        content: [],
        totalItems: 0,
        page: 1,
        totalPages: 1,
    };

    settings = {
        CategoriesDisabled: true,               // Single category mode (No category)
        ServerPagination: false,                // Enable server side pagination (TODO: Not implemented)
        perPage: 11,                            // Number of items per page
    };

    elementQueries = {
        root: "",
        categoriesContainer: ".search__catalog",
        resultsContainer: ".search__search-result-category-row",
        searchInput: "#search-input",
        searchPaginationContainer: ".search__pagination-container",
    };

    elements = {
        root: undefined,
        resultsContainer: undefined,
        categoriesContainer: undefined,
        searchInput: undefined,
        searchPaginationContainer: undefined,
    };

    // Init
    constructor({
            rootQuery = ".search__content-wrapper",
            CategoriesDisabled = this.settings.CategoriesDisabled,
        } = {}) {
        // Set settings, use defaults for not set attributes
        //$.extend(this.settings, arguments[0], this.settings);

        //let _this = this;
        let result;

        result = this.setRoot(rootQuery);
        if(!result){ return }

        this.settings.CategoriesDisabled = CategoriesDisabled;
    }




    // ================== SETUP ================== //
    // Initialize class object. Always run this after new Class
    init(){

        let _this = this;
        let result;

        result = this.setSearchInput();
        if(!result){ return }

        result = this.setResultsContainer();
        if(!result){ return }

        result = this.setSearchPaginationContainert();
        if(!result){ return }

        if(!this.settings.CategoriesDisabled){
            result = this.setCategoriesContainer();
            if(!result){ return }
        }

        this.elements.searchInput.on("input", function () {
            _this.search($(this).val());
        });


        $("body").on("click", this.elementQueries.root+" .search__btn-prev", function () {
            _this.prevPage();
        });
        $("body").on("click", this.elementQueries.root+" .search__btn-next", function () {
            _this.nextPage();
        });

        console.log("[MultiSearch] Init success");
    }

    // Set root element
    setRoot(rootQuery){
        this.elements.root = $(rootQuery);
        if(this.elements.root.length === 0){
            console.error("[MultiSearch] Root element not found");
            return false;
        }
        this.elementQueries.root = rootQuery;
        return true;
    }

    // Set search input
    setSearchInput(){
        this.elements.searchInput = this.elements.root.find(this.elementQueries.searchInput);
        if(this.elements.searchInput.length === 0){
            console.error("[MultiSearch] searchInput element not found");
            return false;
        }
        return true;
    }

    // Set element where categories will be rendered
    setCategoriesContainer(){
        this.elements.categoriesContainer = this.elements.root.find(this.elementQueries.categoriesContainer);
        if(this.elements.categoriesContainer.length === 0){
            console.error("[MultiSearch] categoriesContainer element not found");
            return false;
        }
        return true;
    }

    // Set element where results will be rendered
    setResultsContainer(){
        this.elements.resultsContainer = this.elements.root.find(this.elementQueries.resultsContainer);
        if(this.elements.resultsContainer.length === 0){
            console.error("[MultiSearch] ResultsContainer element not found");
            return false;
        }
        return true;
    }

    // Set search input
    setSearchPaginationContainert(){
        this.elements.searchPaginationContainer = this.elements.root.find(this.elementQueries.searchPaginationContainer);
        if(this.elements.searchPaginationContainer.length === 0){
            console.error("[MultiSearch] searchPaginationContainer element not found");
            return false;
        }
        return true;
    }

    // ================== MAIN ================== //

    // Ajax request with search string
    search(query){
        let _this = this;

        if(query.length < 3){
            return
        }

        let formData = new FormData();
        //formData.append("_token", window.token);
        formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
        formData.append("query", query);

        if(_this.ajax.request !== null ){
            _this.ajax.request.abort();
            _this.ajax.request = null;
        }

        _this.ajax.request = $.ajax({
            timeout: 60000,
            url: _this.ajax.url,
            type: 'POST',
            cache: false,
            data: formData,
            processData: false,
            contentType: false,

            beforeSend: function () {

            },
            success: function (response) {
                if (response['status'] === '1') {
                    _this.data.content = response["content"];
                    _this.calculatePages();
                    _this.data.page = 1;
                    _this.renderSearchResult();
                }
            },
            error: function (response) {

            }
        });
    }

    // Render search result items
    renderSearchResult(){
        let pageItems = this.getPageArray(this.data.page, this.data.content);
        let html = this.templateSearchResultCategory({categoryTitle: "", searchItems: pageItems});
        let paginationHtml = this.templatePaginationButtons();
        this.elements.resultsContainer.html(html);
        this.elements.searchPaginationContainer.html(paginationHtml);
    }

    // Render search result categories (not items)
    renderCategories(){

    }

    prevPage(){
        if(this.data.page < 2){
            return false;
        }

        this.data.page--;
        this.renderSearchResult();
    }

    nextPage(){
        if(this.data.page >= this.data.totalPages){
            return false;
        }

        this.data.page++;
        this.renderSearchResult();
    }

    // ================== HELPERS ================== //
    calculatePages(){
        this.data.totalPages = Math.ceil(this.data.content.length / this.settings.perPage);
    }

    isLastPage(){
        return this.data.page === this.data.totalPages;
    }

    getPageArray(page, array){
        return array.slice((page - 1) * this.settings.perPage, page * this.settings.perPage);
    }

    // ================== TEMPLATES ================== //

    templateCategoryItem = function(){
        return "";
    };

    // Whole search result category
    templateSearchResultCategory({
        categoryTitle = "",
        searchItems = [],
    } = {})
    {
        console.log("[MultiSearch] searchItems", searchItems);
        // Generate search result items
        let searchItemsHtml = "";
        for (let i=0; i<searchItems.length; i++){
            searchItemsHtml += this.templateSearchResultItem({
                id: searchItems[i].id,
                title: searchItems[i].title,
                url: searchItems[i].url,
                picture: searchItems[i].picture,
                oldPrice: "",
                price: searchItems[i].price,
            });
        }

        // Generate search result category
        let html =
        `
        <div class="search__col">
            <div class="search-result-category">
                <div class="search-result-category__head">
                    <span class="search-result-category__head-title">
                        ${categoryTitle}
                    </span>
                    <svg class="search-result-category__head-icon" width="28" height="16">
                        <use xlink:href="/public/img/svg/sprite-search.svg#arrow-icon"></use>
                    </svg>
                </div>
                <div class="search-result-category__body">
                    ${searchItemsHtml}
                </div>
            </div>
        </div>
        `;
        return html;
    }

    // Search result item (child of searchResultCategory)
    templateSearchResultItem({
        id = "",
        picture = "",
        title = "",
        url = "#",
        oldPrice = "",
        price = "",

    } = {})
    {
        let oldPriceHtml = "";
        if(oldPrice !== ""){
            oldPriceHtml =
            `
            <div class="search-result-category__item-old-price">
                ${oldPrice} руб.
            </div>
            `;
        }

        let html =
        `
        <a href="${url}" data-object-id="${id}" data-product-id="${id}" class="search-result-category__item Product_Item_Fn">
            <div class="search-result-category__item-button-buy btn_add_product" data-product-id="${id}">
                <svg class="search-result-category__item-button-buy-icon" width="19" height="12">
                    <use xlink:href="/public/img/svg/sprite-search.svg#buy-icon"></use>
                </svg>
            </div>
            <div class="search-result-category__item-img-wrap">
                <div class="search-result-category__item-img-container">
                    ${picture}
                </div>
            </div>
            <div class="search-result-category__item-info">
                <div class="search-result-category__item-title">
                    ${title}
                </div>
                <div class="search-result-category__item-price-wrap">
                    ${oldPriceHtml}
                    <div class="search-result-category__item-price">
                        ${price} руб.
                    </div>
                </div>
            </div>
        </a>
        `;
        return html;
    }

    // Prev and Next buttons for pagination
    templatePaginationButtons(){
        if(this.data.totalPages < 2){
            return "";
        }

        let prevHtml = "";
        let nextHtml = "";

        if(this.data.page !== 1){
            prevHtml = `
            <div class="search__btn-prev">
                <svg class="search__btn-prev-icon" width="26" height="12">
                    <use xlink:href="uploads/multi-search/sprite-search.svg#carousel-btn-icon"></use>
                </svg>
            </div>
            `;
        }else{
            prevHtml = `
            <div class="search__btn-prev" style="opacity: 0;">
                <svg class="search__btn-prev-icon" width="26" height="12">
                    <use xlink:href="uploads/multi-search/sprite-search.svg#carousel-btn-icon"></use>
                </svg>
            </div>
            `;
        }

        if(this.data.page !== this.data.totalPages){
            nextHtml = `
            <div class="search__btn-next">
                <svg class="search__btn-next-icon" width="26" height="12">
                    <use xlink:href="uploads/multi-search/sprite-search.svg#carousel-btn-icon"></use>
                </svg>
                <div class="search__btn-next-count-wrap">
                    <div class="search__btn-next-count">${this.data.page+1}</div>
                </div>
            </div>
            `;
        }else{
            nextHtml = `
            <div class="search__btn-next" style="opacity: 0;">
                <svg class="search__btn-next-icon" width="26" height="12">
                    <use xlink:href="uploads/multi-search/sprite-search.svg#carousel-btn-icon"></use>
                </svg>
                <div class="search__btn-next-count-wrap">
                    <div class="search__btn-next-count">${this.data.page+1}</div>
                </div>
            </div>
            `;
        }

        let html = `
        <div class="search__btns">
            ${prevHtml}
            ${nextHtml}
        </div>
        `;

        return html;
    }

}