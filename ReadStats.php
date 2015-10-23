<?php 
function getData() {
	$page = $_GET["page"];
	$daysperpage = 100;

	$stats_JSON = file_get_contents("stats.json");
	$stats = json_decode($stats_JSON, true);

	$CurrencyNum = count($stats[count($stats) - 1]["currencies"]);

	$currencyList;

	$jsArray = "['Timestamp','messages'"; //

	foreach ($stats[count($stats) - 1]["currencies"] as $currency => $value) {
		$currencyList[] = $currency;
		$jsArray .= ", '" . $currency."'";
	}
	$jsArray .= "],\n";

	foreach($stats as $stat) {
		if(!$stat["messages"])
			$stat["messages"] = 0;
		$jsArray .= "['" . $stat["ts"]."',".$stat["messages"].""; //.
		foreach ($currencyList as $currency) {
			if (array_key_exists($currency, $stat["currencies"]))
				$count = $stat["currencies"][$currency];
			else
				$count = 0;
			$jsArray .= ", ".$count;
		}
		$jsArray .= "],\n";
	}

	$jsArray = rtrim($jsArray, ",\n");
	return $jsArray;
}
 
?>
<html>
	<head>
		<script type="text/javascript"
						src="https://www.google.com/jsapi?autoload={
						'modules':[{
							'name':'visualization',
							'version':'1',
							'packages':['corechart']
						}]
					}">
		</script>
 
		<script type="text/javascript">
			google.setOnLoadCallback(drawChart);
 
			function drawChart() {
				var data = google.visualization.arrayToDataTable([
					<?=getData($page);?>
				]);
 
				var options = {
					title: 'Sum of the different kind of currencies',
					curveType: 'function',
					legend: { position: 'bottom' },
									vAxis: {
													viewWindow: {
																min:0
																}
												}
				};
 
				var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
 
				chart.draw(data, options);
			}
		</script>
	</head>
	<body>
		<div id="curve_chart" style="width: 100%; height: 500px"></div>
	</body>
</html>
