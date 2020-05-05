<?php

class ShareCounter 
{
	public function getVkCounts($url)
	{
		$answer = file_get_contents('http://vk.com/share.php?act=count&url=' . $url);
		$count = intval(substr($answer, 18, -2));
		return $count;
	}


	public function getOkCounts($url)
	{
		$answer = file_get_contents('https://connect.ok.ru/dk?st.cmd=extLike&tp=json&ref=' . $url);
		$json = json_decode($answer, true);
		$count = isset($json['count']) ? intval($json['count']) : 0;
		return $count;
	}


	public function getTwitterCounts($url) 
	{ 
		$answer = file_get_contents('http://opensharecount.com/count.json?url=' . $url);
		$json = json_decode($answer, true);
		$count = isset($json['count']) ? intval($json['count']) : 0;
		return $count;
	}

	
	public function getFacebookCounts($url)
	{
		$answer = file_get_contents('http://graph.facebook.com/v4.0?fields=og_object{engagement}&id=' . $url);
  		$json = json_decode($answer, true);
  		$count = isset($json['og_object']['engagement']['count']) ? intval($json['og_object']['engagement']['count']) : 0;
  		return $count;
	}


	public function getCounts($url) 
	{
		return [
			'vk' => $this->getVkCounts($url),
			'ok' => $this->getOkCounts($url),
			'twitter' => $this->getTwitterCounts($url),
			'facebook' => $this->getFacebookCounts($url)
		];
	}
}



$counter = new ShareCounter();
print_r($counter->getCounts('https://www.kolesa.ru/'));
