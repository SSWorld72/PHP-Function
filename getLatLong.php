<?php

  //測試用
	//try {
	//	$location = getLatLong('台北火車站');
	//	echo "查詢的經緯度為：{$location['lat']}, {$location['lng']}";
	//} catch (Exception $e) {
	//	echo '發生錯誤：' . $e->getMessage();
	//}

	// 透過 Google Maps Geocoding API 將地址或地名轉換為經緯度
  // 請記得修改 XXXXXXXXXXXXXXXXXXXXXXXXXX 及 xxxxx.yyy.com.tw
	function getLatLong($address){

		// 驗證輸入：確保輸入為字串，否則拋出異常
		if (!is_string($address)) {
			throw new Exception('請輸入地址文字字串或地標名稱');
		}

		// 注意：請將 API 密鑰替換為您申請的實際 API 密鑰，若無請前往 https://console.cloud.google.com/apis/ 申請
		$gmap_api_key = 'XXXXXXXXXXXXXXXXXXXXXXXXXX';
		
		// 設定 Google Maps Geocoding API 的請求 URL
		$url = "https://maps.googleapis.com/maps/api/geocode/json?key=".$gmap_api_key."&language=zh-TW&address={".$address."}";

		//使用 cURL 發送 HTTP GET 請求
		$ch = curl_init();
		
		//設定要請求的 URL 地址
		curl_setopt($ch, CURLOPT_URL, $url);
		
		//將 cURL 的執行結果作為一個字串返回，而不是直接輸出
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //檢查是否為對外網頁伺服器
		if ($_SERVER['HTTP_HOST'] !== 'xxxxx.yyy.com.tw'){  //請將其修改成該對外網頁伺服器的完全合格網域名稱，也就是 FQDN
			//關閉伺服器 SSL 證書的驗證，避免異常（限無對外服務的內部開發環境下使用，可對外服務的伺服器環境請停用關閉）
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);	// 是否驗證伺服器 SSL 證書的主機名
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);	// 是否驗證伺服器 SSL 證書的有效性
		}
		
		//強制使用 HTTP/1.1（如果可以支援 HTTP/2，則請停用）
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		
		// 執行請求並取得回應
		$response = curl_exec($ch);
		
		// 檢查是否有錯誤發生
		if(curl_errno($ch)) {
			echo 'Curl error: ' 。 curl_error($ch);
			// 關閉 cURL 句柄
			curl_close($ch);
		} else {
			// 關閉 cURL 句柄
			curl_close($ch);
			// 解碼 JSON 並處理結果（轉換為 PHP 陣列）
			$result = json_decode($response, true);

			// 當請求失敗時，顯示錯誤訊息，並終止程式
			if ($result['status'] !== 'OK') {
				throw new Exception("Google Maps API 請求失敗：{$result['status']}，錯誤訊息：{$result['error_message']}");
			}
			
			// 提取經緯度
			$location = [
				'lat' => $result['results'][0]['geometry']['location']['lat']，
				'lng' => $result['results'][0]['geometry']['location']['lng']
			];
			
			// 回傳陣列資料
			return $location;
		}
	}

?>
