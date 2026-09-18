<?
	function Paging($current_page, $total_data_per_page, $total_data, $target, $pr) {
		$total_visible_page = 5;
		$total_page = ceil($total_data / $total_data_per_page);
		$result = "";

		if ($current_page <= 1) {
			$result .= "First | ";
		} else {
			$result .= "<a href=\"$target" . "1" . $pr . "\">First</a> | ";
		}

		if ($current_page <= 1) {
			$result .= "Prev | ";
		} else {
			$prev_page = $current_page - 1;
			$result .= "<a href=\"$target" . "$prev_page" . $pr . "\">Prev</a> | ";
		}

		if ($current_page > $total_page - floor($total_visible_page / 2)) {
			$first_page = $total_page - $total_visible_page;
		} else {
			$first_page = $current_page - floor($total_visible_page / 2);
		}

		if ($first_page < 1) {
			$first_page = 1;
		}

		if ($current_page < 1 + floor($total_visible_page / 2)) {
			$last_page = $total_visible_page;
		} else {
			$last_page = $current_page + floor($total_visible_page / 2);
		}

		if ($last_page > $total_page) {
			$last_page = $total_page;
		}

		for ($i = $first_page; $i <= $last_page; $i++)
		{
			if ($i == $current_page) {
				$result .= "$i | ";
			} else {
				$result .= "<a href=\"$target" . "$i" . $pr . "\">$i</a> | ";
			}
		}

		if ($current_page >= $total_page) {
			$result .= "Next | ";
		} else {
			$next_page = $current_page + 1;
			$result .= "<a href=\"$target" . "$next_page" . $pr . "\">Next</a> | ";
		}

		if ($current_page >= $total_page) {
			$result .= "Last";
		} else {
			$result .= "<a href=\"$target" . "$total_page" . $pr . "\">Last</a>";
		}

		return $result;
	}
?>
