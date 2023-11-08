<?php
use think\facade\Db;
function OpenSSL($data){
    $set=config('site');
    $path=root_path().$set['document'].$data['class'].md5($data['commonName']);
    if(!is_dir($path)){
	    mkdir($path,0777,true);
	}
    $dn = array(
        "countryName" => $data['countryName'], //所在国家名称
        "stateOrProvinceName" => $data['stateOrProvinceName'], //所在省份名称
        "localityName" => $data['localityName'], //所在城市名称
        "organizationName" => $data['organizationalUnitName'], //注册人姓名
        "organizationalUnitName" => $data['organizationalUnitName'], //组织名称
        "commonName" => $data['commonName'], //公共名称
        //"emailAddress" => $data['emailAddress'], //邮箱
    );
    $configpath = root_path().$set['document'];
    if(!is_dir($configpath)){
	    mkdir($configpath,0777,true);
	}
    
    if($data['ssl_ca_id'] == '0'){
        $configcnf = file_get_contents($configpath."/config/RootCA.cnf");
        
    }else{
        if($data['class'] == '/server/'){
            $configcnf = file_get_contents($configpath."/config/server.cnf");
            $domain = 'DNS:'.$data['commonName'];
            if(!empty($data['domains'])){
                $domains = explode(",", $data['domains']);
                foreach ($domains as $value) {
                    $domain .= ',DNS:'.$value;
                }
            }
            if(!empty($set['crt'])){
                $crt = "authorityInfoAccess = 'caIssuers;URI:".$set['crt']."'";
            }else{
                $crt = '';
            }
            if(!empty($set['crl'])){
                $crl = "crlDistributionPoints=URI:".$set['crl'];
            }else{
                $crl = '';
            }
            if(!empty($set['CPS'])){
                $CPS = "CPS.1=".$set['CPS'];
            }else{
                $CPS = '';
            }
            $configcnf = str_replace('{crt}',$crt,$configcnf);
            $configcnf = str_replace('{crl}',$crl,$configcnf);
            $configcnf = str_replace('{CPS}',$CPS,$configcnf);
            $configcnf = str_replace('{domain}',$domain,$configcnf);
            $configcnf = str_replace('{oname}',$data['organizationalUnitName'],$configcnf);
        }else{
            $configcnf = file_get_contents($configpath."/config/MiddleCA.cnf");
        }
    }
    file_put_contents($path."/config.cnf",$configcnf);
    //加密方式
    $config = array(
        "digest_alg" => $set['digest_alg'],
        "private_key_bits" => (int)$set['private_key_bits'], //字节数 512 1024 2048 4096 等
        "private_key_type" => (int)$set['private_key_type'], //加密类型
        "config" => $path."/config.cnf"//指定证书配置文件
    );
    //生成证书数据
    $privkey = openssl_pkey_new($config);
    if($data['ssl_ca_id'] == '0'){
        $data['cacert'] = NULL;
        $data['cakey'] = $privkey;
    }else{
        $name = Db::name('ssl_ca')->where('id', $data['ssl_ca_id'])->value('name');
        $capath=root_path().$set['document'].'/authority/'.md5($name);
        $data['cacert'] = file_get_contents($capath."/certificate.crt");
        $data['cakey'] = file_get_contents($capath."/certificate.key");
    }
    $csr = openssl_csr_new($dn, $privkey);
    $scert = openssl_csr_sign($csr, $data['cacert'], $data['cakey'], (int)$data['day'] , $config ,(int)$data['serial']);
    //导出证书文件
    openssl_pkey_export_to_file($privkey,$path."/certificate.key",NULL, $config);
    openssl_x509_export_to_file($scert,$path."/certificate.crt");
    openssl_csr_export_to_file($csr,$path."/certificate.csr");
    if($data['class'] == '/server/'){
        $pem = file_get_contents($path."/certificate.crt");
        $pem .= $data['cacert'];
        $myfile = fopen($path."/certificate.pem", "a") or die("Unable to open file!");
        fwrite($myfile, $pem);
	    fclose($myfile);

    }
}
