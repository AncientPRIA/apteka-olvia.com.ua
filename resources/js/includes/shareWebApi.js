

const btnShareWebApi =(params,callbackError,callbackSuccess)=>{

	if(params === undefined){
		params ={};
	}

	let {
		url= window.location.href,
		shareId="social-share-other",
		elClassInsert="social-share",
		toInsert="beforeend",
		shareClassList="",
		shareIcon="",
		title="",
		text="",
	} = params;

		if (navigator.share) {
			const icon = shareIcon !=="" ? shareIcon : `<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
							 viewBox="0 0 426.667 426.667" style="enable-background:new 0 0 426.667 426.667;" xml:space="preserve">
								<path d="M352,256c-27.902,0-51.993,15.57-64.807,38.307l-139.844-53.785c1.214-5.333,1.984-10.827,1.984-16.522
									c0-7.388-1.41-14.385-3.418-21.13l143.579-66.275c13.333,20.458,36.32,34.072,62.505,34.072c41.167,0,74.667-33.5,74.667-74.667
									c0-41.167-33.5-74.667-74.667-74.667S277.333,54.833,277.333,96c0,7.388,1.41,14.385,3.418,21.13l-143.579,66.275
									c-13.333-20.458-36.32-34.072-62.505-34.072C33.5,149.333,0,182.833,0,224c0,41.167,33.5,74.667,74.667,74.667
									c27.902,0,51.994-15.57,64.807-38.307l139.844,53.785c-1.214,5.333-1.984,10.827-1.984,16.522c0,41.167,33.5,74.667,74.667,74.667
									s74.667-33.5,74.667-74.667C426.667,289.5,393.167,256,352,256z M352,42.667c29.406,0,53.333,23.927,53.333,53.333
									S381.406,149.333,352,149.333S298.667,125.406,298.667,96S322.594,42.667,352,42.667z M74.667,277.333
									c-29.406,0-53.333-23.927-53.333-53.333s23.927-53.333,53.333-53.333S128,194.594,128,224S104.073,277.333,74.667,277.333z
									 M352,384c-29.406,0-53.333-23.927-53.333-53.333s23.927-53.333,53.333-53.333s53.333,23.927,53.333,53.333S381.406,384,352,384z"
									/>
						</svg>` ;

			document.querySelector("."+elClassInsert).insertAdjacentHTML(toInsert, `
					<span style="background-color: #673AB7;" id="social-share-other" class="social-share__item social-share__other ${shareClassList}">
						${icon}
					</span>
			`);

			document.addEventListener('click',function(e){
				console.log(e.target.id);
				if(e.target && e.target.id== shareId){

					//let title_link = document.querySelector("."+title).dataset.share;
					// const msg_link = document.querySelector("."+text).dataset.share;
						navigator.share({
							title: title,
							text: text,
							url: url
						})
							.then(function () {
								if(callbackSuccess !== undefined){
									callbackSuccess();
								}
							})
							.catch(function (){
								if(callbackError !== undefined){
									callbackError()
								}
							})

				}
			});

		} //else {
			//console.log("Sorry! Your browser does not support Web Share API")
		//}
}


export default btnShareWebApi;
