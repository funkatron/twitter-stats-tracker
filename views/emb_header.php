<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<title><?=$page_title?></title>

	<link rel="stylesheet" href="/assets/tss-css/tss.css" type="text/css" charset="utf-8">

	<script src="http://www.google.com/jsapi"></script>
	<script type="text/javascript" charset="utf-8">
		google.load("jquery", "1");
	</script>
	
	<script language="JavaScript" src="/assets/jquery.rule-1.0.1.1-min.js"></script>
	<script language="JavaScript" src="/assets/jquery.event.drag.custom.js"></script>
	<script language="JavaScript" src="/assets/slick.grid.js"></script>
	<script language="JavaScript" src="/assets/slick.model.js"></script>

	<script type="text/javascript" charset="utf-8">
	var grid;
	var dataView;
	var sortcol = "source";
	var sortdir = 1;
	var searchString = "";
	var data = [], rows = [];
	
	<?php
		function cmp($a, $b) {
			if ($a->count == $b->count) {
				return 0;
			}
			return ($a->count > $b->count) ? -1 : 1;
		}
		usort($data->results, 'cmp')
	?>
	<?$x=1;?>
	<?foreach($data->results as $result):?>
	data.push({ "id":"<?='id_'.$x;?>", "rank":"<?=$x++;?>", "link":"<?=$result->link?>", "source":"<?=$result->source?>", "percent":"<?=number_format(($result->count/$data->total)*100, 3)?>"});
	<?endforeach;?>
	

	function myFilter(item) {
		if (searchString != "" && item["source"].indexOf(searchString) == -1) {
			return false;
		}
		return true;
	}

	function comparer(a,b) {
		var x = a[sortcol], y = b[sortcol];
		return (x == y ? 0 : (x > y ? 1 : -1));
	}
	
	function numeric_comparer(a,b) {
		var x = a[sortcol]*1, y = b[sortcol]*1;
		return (x == y ? 0 : (x > y ? 1 : -1));
	}
	

	var addLinkFormatter = function(row, cell, value, columnDef, dataContext) {
		if (dataContext.link.indexOf('http') !== -1) {
			return "<a href='"+dataContext.link+"' target='_blank'>"+value+"</a>";
		}
		return value;
	};
	

	var columns = [
		{id:"rank", name:"Rank", field:"rank",width:70,sortable:true},
		{id:"app", name:"App", field:"source",width:400, formatter:addLinkFormatter,sortable:true},
		{id:"percent", name:"%", field:"percent",width:70,sortable:true}
	];

	var options = {
		'enableCellNavigation' : false,
		'enableColumnReorder'  : false,
		'enableCellNavigation' : true,
		'forceFitColumns'      : true
	};	
	
	</script>
	
	
	<script type="text/javascript" charset="utf-8">
		$().ready( function() {

						
			dataView = new Slick.Data.DataView();
			grid = new Slick.Grid($("#myGrid"), dataView.rows, columns, options);


			grid.onSort = function(sortCol, sortAsc) {
				sortdir = sortAsc ? 1 : -1;
				sortcol = sortCol.field;
				// using native sort with comparer
				// preferred method but can be very slow in IE with huge datasets
				if (sortCol.field == 'source') {
					dataView.sort(comparer,sortAsc);
				} else {
					dataView.sort(numeric_comparer,sortAsc);
				}

			};

			// wire up model events to drive the grid
			dataView.onRowCountChanged.subscribe(function(args) {
				grid.updateRowCount();
			});

			dataView.onRowsChanged.subscribe(function(rows) {
				grid.removeRows(rows);
				grid.render();

			});

			$('#search_app_name').keyup(function(e) {
				// clear on Esc
				if (e.which == 27)
					this.value = "";

				searchString = this.value;
				dataView.refresh();
			});
			
			// initialize the model after all the events have been hooked up
			dataView.beginUpdate();
			dataView.setItems(data);
			dataView.setFilter(myFilter);
			dataView.endUpdate();
		});
	</script>
	
</head>


<body>
	<div id="funkatron-bar">
		<div id="funkatron-bar-container">
			<span id="blogtitle">&#x238B; <a href="http://funkatron.com/">funkatron.com</a></span>
			<ul id="navbar">
				<li id="home"><a href="http://funkatron.com/" title="Home">Home</a></li>
				<li id="about"><a href="http://funkatron.com/about/" title="About">About</a></li>
				<li id="archives"><a href="http://funkatron.com/site/archives/" title="Archives">Archives</a></li>
				<li id="contact"><a href="mailto:POW POW POW coj AT funkatron DOT com POW POW POW">Contact</a></li>
				<li id="rssfeed"><a href="http://funkatron.com/site/rss_2.0/"><img src="http://funkatron.com/images/feed-icon-14x14.png" /> RSS</a></li>
			</ul>
		</div>
	</div>
