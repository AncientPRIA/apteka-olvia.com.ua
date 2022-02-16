// general delete handler
$("body").on("click",".del-item",function(){
	var data = $(this).data("del");
	data = data.split("|");

	$(`.${data[1]} .table-list__item[data-del="${data[0]}"]`).remove();
	console.log(data[1]);
	if(data[1] != "rez-hole"){
		$(`.${data[1]} .rez-hole_counter`).text($(`.${data[1]} .table-list__item`).length);
	}else{
		$(`.${data[1]} .rez-cutout_counter`).text($(`.${data[1]} .table-list__item`).length);
	}

});

// add drilling

$(".add-hole").on("click",function(){

	var obj = {...value_collection($(".sverlo select")), ...value_collection($(".sverlo input"))};
	var len_items = $(".rez-hole .table-list__item").length + 1;

	$(".rez-hole .widget-info-list").append(`
			<li class="table-list__item table-item_bgc-1 " data-del="${len_items}">
				<div class="table-text column_1_1 column-gen">
					Сторона: ${obj.hole_type}
				</div>
            	<div class="table-image-item column_1_1  column-gen">
					Вид отверстия : ${obj.side}
				</div>
				<div class="table-text column_1_1  column-gen">
					Привязка к краю : ${obj.snap_to_edge_1} - ${obj.snap_to_edge_2}
				</div>
				<div class="table-text column_1_1  column-gen">
					Координаты : x = ${obj.coordinates_x}; y = ${obj.coordinates_y}
				</div>
				<div class="table-text column_1_1  column-gen">
					Смещение : ${obj.offset_1} - ${obj.offset_2}
				</div>
						
				<div class="table-text column_1_1 column-gen">
					<div class="del-item" data-del="${len_items}|rez-hole" >X</div>
				</div>
			
			</li>
	`);

	$(".rez-hole_counter").text(len_items);

});

// add

$(".add-cutout").on("click",function(){

	var obj = {...value_collection($(".virez select")), ...value_collection($(".virez input"))};
	var len_items = $(".rez-cutout .table-list__item").length + 1;

	$(".rez-cutout .widget-info-list").append(`
			<li class="table-list__item table-item_bgc-1 " data-del="${len_items}">
				<div class="table-text column_1_1 column-gen">
					Сторона: ${obj.hole_type}
				</div>
            	<div class="table-image-item column_1_1  column-gen">
					Вид отверстия : ${obj.side}
				</div>
				<div class="table-text column_1_1  column-gen">
					Привязка к краю : ${obj.snap_to_edge_1} - ${obj.snap_to_edge_2}
				</div>
				<div class="table-text column_1_1  column-gen">
					Координаты : x = ${obj.coordinates_x}; y = ${obj.coordinates_y}
				</div>
				<div class="table-text column_1_1  column-gen">
					Смещение : ${obj.offset_1} - ${obj.offset_2}
				</div>
						
				<div class="table-text column_1_1 column-gen">
					<div class="del-item" data-del="${len_items}|rez-cutout" >X</div>
				</div>
			
			</li>
	`);

	$(".rez-cutout_counter").text(len_items);

});


// functions

function value_collection(elements){
	var arr_val = {};

	for(var i=0;i<elements.length;i++){
		arr_val[elements.eq(i).attr("name")] = elements.eq(i).val()
	}

	return arr_val
}
