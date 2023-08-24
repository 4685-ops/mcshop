<?php

$curl = curl_init();
$url = "https://www.taptap.cn/webapiv2/app-top/v2/hits?from=0&limit=10&platform=android&type_name=independent&X-UA=V%3D1%26PN%3DWebApp%26LANG%3Dzh_CN%26VN_CODE%3D102%26VN%3D0.1.0%26LOC%3DCN%26PLT%3DPC%26DS%3DAndroid%26UID%3D78c8778b-70f7-4b4d-b387-63b02f7216a9%26DT%3DPC%26OS%3DWindows%26OSV%3D7.0.0";
curl_setopt_array($curl, [
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => [
    "Accept-Encoding: gzip, deflate, br",
    "Accept-Language: zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2",
    "Cache-Control: no-cache",
    "Connection: keep-alive",
    "Cookie: web_app_uuid=8c26982a-771b-410b-a53c-e3efe2dc7b55; apk_download_url_postfix=/organic-direct; locale=zh_CN; tap_theme=light; _gid=GA1.2.434312544.1692777685; Hm_lvt_536e39e029b26af67aecbc444cdbd996=1692012213,1692777685; XSRF-TOKEN=dxh2tmghastz5ltp6ugm; _clck=mwagd1|2|fef|0|1321; ssxmod_itna2=Qq0xcD0DR7itD=GkQDXzG7AHG==POAxOixwG+eA6n6d4+5D/+UUDFO7M2UZatyKGFzjdbjxy4F0Khw3etY+6dq+E7G0N9mEaoFwCezxxu7HzKW26IM436ognpwX/A01HzjoFM=9QSHicWKx+aHGq9mxK/5s+rFTPKmx7EA7MnW8rYg+Kmya5ewbmcDFheqODEI1f=pa9A0TClfdUb9aT6vCHj7jAtPjVFvCmrpxED07YeqDLxG7YYD==; acw_tc=7b39758216928442384842516e6a8f4b138cbbd3bbfa51faefd3260a1cc74b; acw_sc__v2=64e6c0cf144130f9a3baec357c591a80e8127f1c; _gat=1; _ga_6G9NWP07QM=GS1.1.1692844238.8.0.1692844238.0.0.0; Hm_lpvt_536e39e029b26af67aecbc444cdbd996=1692844239; _clsk=d2mn2l|1692844238834|1|0|u.clarity.ms/collect; ssxmod_itna=Gqmh0IqUxAxf2xl+x+obNGCYqKwWxQTYucPDsWbDSxGKidDqxBWnduDDQregetgKABe0iH9mda0mWiz3WIqvbUwieDHxY=DUPRSGYD48KGwD0eG+DD4DWIx03DoxGABhx0+8Bn6LIQGRD0YDzqDgD7jVlqDfWMGzWkUj3lXqxGUiIw43WqDMD7tD/Rhiur=DGTWbuoXgRCTDbgeRKCiDtqD9WQU6teDH8kXS4qFz7Bei+A30CYpiA7GQ7YvitAmNA0LvOixzGG+CCivQj0+1QHD=; _ga=GA1.2.269727936.1692012213",
    "Pragma: no-cache",
    "Sec-Fetch-Dest: document",
    "Sec-Fetch-Mode: navigate",
    "Sec-Fetch-Site: none",
    "Sec-Fetch-User: ?1",
    "TE: trailers",
    "Upgrade-Insecure-Requests: 1"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
