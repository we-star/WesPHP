<script>
	$(document).ready(function(){
		$("#applyfor").on("hide.bs.collapse", function(e){
			var name = $(e.target).attr("id");
			$("h6[data-target='#" + name + "']").find("i").removeClass("fa-chevron-down").addClass("fa-chevron-right");
		});

		$("#applyfor").on("show.bs.collapse", function(e){
			var name = $(e.target).attr("id");
			$("h6[data-target='#" + name + "']").find("i").removeClass("fa-chevron-right").addClass("fa-chevron-down");
		});
	});
</script>
<div class="list-group">
	<a href="/applyfor/pos" class="list-group-item list-group-item-action {if $smarty.get.target == 'applyfor/pos'}active{/if}">POS门店申请加盟</a>
	<a href="/applyfor/sp" class="list-group-item list-group-item-action {if $smarty.get.target == 'applyfor/sp'}active{/if}">代理商申请加盟</a>
</div>