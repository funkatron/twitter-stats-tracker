<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<title><?=$page_title?></title>


	<style type="text/css" media="screen">
		@import "/assets/jquery.tablesorter/themes/blue/style.css";
	
		BODY {
			font-family: Palatino, 'Palatino Linotype', Georgia, Times, 'Times New Roman', "MS Trebuchet", serif;
			padding:0;
			margin:0;
		}
		#container {
			margin-left:auto;
			margin-right:auto;
			width:700px;
		}

		h1, h2, #nav {
			margin-left:auto;
			margin-right:auto;
			width:600px;
		}
		
		h2{
			font-size:13pt;
		}
		
		#nav ul {
			padding-left:0px;
			list-style: none;
			white-space: nowrap;
		}
		
			#nav ul li {
				display:inline;
				margin: 0;
				padding:0;
				list-style:none;
			}
		
				#nav ul li a {
					font-size:10pt;
					-webkit-border-radius:10px;
					-moz-border-radius:   10px;
					border-radius:        10px;
					background-color:#DDDDCC;
					padding:3pt 6pt;
					margin-right:5pt;
					white-space: nowrap;
				}
				#nav ul li a:link, #nav ul li a:visited, #nav ul li a:active {
					text-decoration:none;
					color:#333333;
				}
		
		#full-list {
			margin-left:auto;
			margin-right:auto;
			width:600px;
		}
		#chart {
			text-align:center;
		}
		#note {
			font-size:9pt;
			padding:10px;
			margin:10px;
			background-color:#FFFFCC;
			border-top:3px double #999;
			border-bottom:3px double #999;
			
			color:#666;
			margin-left:auto;
			margin-right:auto;
			width:500px;
			
			line-height:1.5em;
		}
		
		#ad {
			font-size:9pt;
			padding:10px;
			margin:10px;
			background-color:#CCCCFF;
			border-top:3px double #333333;
			border-bottom:3px double #333333;
			
			color:#000000;
			margin-left:auto;
			margin-right:auto;
			width:500px;
			
			line-height:1.5em;
		}
		
		#note p+p {
/*			padding:5px 0 0 0 ;*/
			margin: .5em 0 0 0;
		}
	</style>
	
	<style type="text/css" media="screen">


		#funkatron-bar {
			font-family: Palatino, Palatino MS, Georgia, Times, Serif;
			border-top:2px solid #000;
			border-bottom:2px solid #000;
/*			background: #9C9B7A;*/
			background: #202020;
			z-index: 50;
/*			opacity: 0.4;*/
			color: #999999;
			padding:5px 5px 3px;

		}
			#funkatron-bar-container {
				margin-left:auto;
				margin-right:auto;
				width:900px;
				text-align:center;
/*				border:1px solid green;*/
			}

				#blogtitle {
					color: #FFFFFF;
					text-transform: uppercase;
				}

				#blogtitle a:link, #blogtitle a:active, #blogtitle a:visited {color:#AAAAAA; text-decoration:none;}

				#funkatron-bar ul {
					display:inline;
					list-style:none;
					padding:0;
					margin:0 0 0 175px;
				}
					#funkatron-bar li {
						display:inline;
						padding:0;
						margin:0 0 0 1em;
					}

						#funkatron-bar li a, #funkatron-bar li a:visited {
							text-transform: uppercase; 
							color: #AAAAAA;
							text-decoration:none;
						}
						#funkatron-bar li a:hover, #blogtitle a:hover {
							background-color:transparent !important;
				/*			border-bottom: 1px solid #FFFFFF !important;*/
							color: #FFFFFF !important;
						}
						#rssfeed img {vertical-align:-10%;}

	</style>

	<script src="http://www.google.com/jsapi"></script>
	<script type="text/javascript" charset="utf-8">
		google.load("jquery", "1");
	</script>
	<script type="text/javascript" charset="utf-8" src="/assets/jquery.tablesorter/jquery.tablesorter.min.js"></script>
	<script type="text/javascript" charset="utf-8">
		$().ready( function() {
			$('#full-list').tablesorter({sortList: [[1,1], [0,0]]});
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