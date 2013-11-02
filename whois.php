<?php
	//==================================================================================
	//
	//		Simple Easy WHOIS API for PHP
	//		Developed By Seung Yeon, You
	//
	//==================================================================================
	//
	//		Get Latest At :
	//		[http://project.seungyeon.me/SimpleEasyWHOISAPI/download]
	//		Price : Free
	//		License : Seung Yeon, You Project License
	//		Check License At :
	//		[ http://project.seungyeon.me/license ]
	//
	//==================================================================================
	//
	//		CONFIGURATION / 설정
	//
	//==================================================================================
	//
	//		file_get_contents가 외부 URL에 사용 가능한 PHP5 이상 : 	0
	//		$sAPIType = '0';
	//
	//		file_get_contents가 외부 URL에 사용 불가능 PHP5 이하 : 	1
	//		$sAPIType = '1';
	//
	//		0일 경우 file_get_contents를 사용하고,
	//		1일 경우 curl을 사용한다.
	//
	//		위에 설명된 이외의 값은 건드리면 동작 오류 발생 가능.
	//
	//==================================================================================
	//
	//					  Version : 0.0.1 (20131101)
	//
	//==================================================================================
	//
	//						  How To Use / 사용법
	//
	//==================================================================================
	//
	//						getWHOISInfo(DOMAIN)
	//					은 해당 도메인의 후이즈 값을 리턴한다.
	//					preWrap(getWHOISInfo(DOMAIN))
	//				   은 도메인 후이즈값을 pre로 싸서 리턴한다.
	//
	//==================================================================================
	
	function getWHOISInfo($url){
		//CONFIGURATION START / 설정 시작
			$sAPIType = '1';
		//CONFIGURATION END / 설정 끝
		//이 이하로는 값 변경시 정상 작동이 이루어지지 않을 수 있음
		/*
				이 이하값을 건드려서 작동하지 않는 경우는 보장하지 않음.
		*/
		$sAPITargetServer = 'KR';
		$sAPIParseEngine = 'EXP';
		if($sAPITargetServer=='KR'){
			$sAPITargetServerURL = 'http://whois.kr/kor/whois.jsc';
		}else{
			ConfErrorView('$sAPITargetServer',$sAPITargetServer,'90','KR');
		}
		
		if($sAPIParseEngine=='EXP'){
			$sAPIParseEngine = 'EXP';
		}else{
			ConfErrorView('$sAPIParseEngine',$sAPIParseEngine,'91','EXP');
		}
		
		if($sAPIType=='0'){
			$sAPIData = fetchDefault($url,$sAPITargetServerURL);
		}elseif($sAPIType=='1'){
			$sAPIData = fetchCURL($url,$sAPITargetServerURL);
		}else{
			ConfErrorView('$sAPIType',$sAPIType,'52','0 OR 1');
		}
		
		$sAPIResult = parseData($sAPIParseEngine,$sAPIData);
		
		return $sAPIResult;
	}
	
	function ConfErrorView($evN,$evV,$evL,$evR){
		echo '<h1>ERROR Occured : 오류 발생.</h1><hr>'.$evN.' 값의 설정이 올바르지 않습니다. 다시 확인해주세요.<br>오류가 있는 값은 '.$_SERVER['PHP_SELF'].'의 '.$evL.' 라인에 위치해 있습니다.<br>올바른 값은 <i>'.$evR.'</i> 어야 합니다.<br><br>현재 '.$evN.'의 설정상태는 다음과 같습니다 : <br><blockquote><i>'.$evN.' = \''.$evV.'\';</i></blockquote>';
		die;
	}
	
	function fetchDefault($url,$server){
		$sAPIPostQuery = http_build_query(
			array(
				'query' => $url
			)
		);

		$sAPIOption = array('http' =>
			array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $sAPIPostQuery
			)
		);

		$sAPIStreamContext  = stream_context_create($sAPIOption);

		$sAPIFetched = file_get_contents($server, false, $sAPIStreamContext);
		
		return $sAPIFetched;
	}
	
	function fetchCURL($req,$server){

		$sAPICurl = curl_init($server);
		curl_setopt($sAPICurl, CURLOPT_POST, true);
		curl_setopt($sAPICurl, CURLOPT_POSTFIELDS, 'query='.$req);
		curl_setopt($sAPICurl, CURLOPT_RETURNTRANSFER, true);
		$sAPIFetched = curl_exec($sAPICurl);
		
		return $sAPIFetched;
	}
	
	function parseData($engine,$data){
		if($engine=='EXP'){
			$sAPIParseData = $data;
			unset($data);
			
			$sAPIParseData = explode('<br>', $sAPIParseData);
			$sAPIParseDataCount = count($sAPIParseData);
			$sAPIParseDataCount--;
			$sAPIParseData = $sAPIParseData[$sAPIParseDataCount];
			
			$sAPIParseData = explode('</pre>', $sAPIParseData);
			$sAPIParseData = $sAPIParseData['0'];
			
			return $sAPIParseData;
		}else{
			ConfErrorView('$sAPIParseEngine',$sAPIParseEngine,'91','EXP');
		}
	}
	
	function preWrap($string){
		$sAPIWrapReturn = '<pre>'.$string.'</pre>';
		return $sAPIWrapReturn;
	}
?>
