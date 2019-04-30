<?php

session_start();
//if($_SERVER['REMOTE_ADDR'] != "94.199.49.145"){ //office 

$accept_ip[] = "94.199.49.145"; 
$accept_ip[] = "193.227.199.59"; 

if(!in_array($_SERVER['REMOTE_ADDR'],$accept_ip)){ //office 
    echo "error i";
    exit;
}
//echo "viewitem_start";

if($uid == ''){
    $uid=trim($_GET['uid']);
}
if($owner == ''){
    $owner=$_SESSION['newuid'];
}
//var_dump($owner);
//var_dump($uid);
//var_dump($_SESSION);
//phpinfo();
$seccheck=0;

//require_once("includes/functions.php");
/*
if ( $type == "invoice" )
{
    $testnum = sqlnumsingle( "SELECT id FROM client_invoices WHERE ownerid='{$owner}' AND id='{$uid}'" );
}else{
    return $testnum;
}

*/

function get_user_istats( $owner, $id, $invid )
{
    $userstats = array( );
    $DIR = dirname( __FILE__ );
    require( $DIR."/includes/dbconfig.php" );
    $userstats[credit_balance] = sqlsingle( "SELECT * FROM accounts WHERE ownerid='{$owner}'", "amount" );
    if ( !is_numeric( $id ) && $id != "" )
    {
        exit( );
    }
    else
    {
        $id = mysql_real_escape_string( $id );
    }
    if ( $owner != "" )
    {
        $query = "SELECT * FROM users WHERE username='{$owner}' limit 1";
        $userid = sqlsingle( "SELECT id FROM users WHERE username='{$owner}'", "id" );
    }
    if ( $id != "" )
    {
        $query = "SELECT * FROM users WHERE id='{$id}' limit 1";
    }
    $result = mysql_query( $query );
    $num = @mysql_numrows( @$result );
    $i = 0;
    while ( $i < $num )
    {
        $userstats[ID] = mysql_result( $result, $i, "id" );
        $userstats[user] = mysql_result( $result, $i, "username" );
        $userstats[isadmin] = mysql_result( $result, $i, "admin" );
        $userstats[isactive] = mysql_result( $result, $i, "active" );
        $userstats[fax] = mysql_result( $result, $i, "fax" );
        $userstats[rspchoice] = mysql_result( $result, $i, "rspchoice" );
        $userstats[jobtitle] = mysql_result( $result, $i, "jobtitle" );
        $userstats[pass1] = mysql_result( $result, $i, "password" );
        $userstats[pass2] = mysql_result( $result, $i, "password" );
        $userstats[passc3] = mysql_result( $result, $i, "password" );
        $userstats[discount] = mysql_result( $result, $i, "discount" );
        $userstats[maillist] = mysql_result( $result, $i, "spam" );
        ++$i;
    }
    $sql = mysql_query( "SELECT * FROM client_invoices WHERE id='{$invid}'" );
    $fetch_em = mysql_fetch_array( $sql );
    $numrows = mysql_num_rows( $sql );
    $userstats['fname'] = $fetch_em['fname'];
    if ( trim( $userstats['fname'] ) != "" && trim( $userstats['fname'] ) != NULL )
    {
        $userstats['fname'] = html_entity_decode( $fetch_em['fname'] );
        $userstats['lname'] = html_entity_decode( $fetch_em['lname'] );
        $userstats['phone'] = $fetch_em['phone'];
        $userstats['email'] = $fetch_em['email'];
        $userstats['add1'] = html_entity_decode( $fetch_em['add1'] );
        $userstats['add2'] = html_entity_decode( $fetch_em['add2'] );
        $userstats['city'] = html_entity_decode( $fetch_em['city'] );
        $userstats['state'] = $fetch_em['state'];
        $userstats['province'] = html_entity_decode( $fetch_em['state'] );
        $userstats['rspchoice'] = $fetch_em['rspchoice'];
        if ( $userstats['rspchoice'] == "2" )
        {
            $userstats['province'] = $fetch_em['state'];
            $userstats['strStateValue'] = $fetch_em['state'];
        }
        else
        {
            $userstats['state'] = $fetch_em['state'];
            $userstats['strStateValue'] = $fetch_em['state'];
        }
        $userstats['zip'] = $fetch_em['zip'];
        $userstats['org'] = html_entity_decode( $fetch_em['org'] );
        $userstats['country'] = $fetch_em['country'];
        $userstats['custom1'] = $fetch_em['custom1'];
        $userstats['custom2'] = $fetch_em['custom2'];
        $userstats['custom3'] = $fetch_em['custom3'];
    }
    else
    {
        $userarray = mysql_fetch_array( mysql_query( "SELECT fname,lname,phone,email,add1,add2,city,state,zip,country,custom1,custom2,custom3,province,rspchoice,org FROM users WHERE id='{$userid}'" ) );
        $userstats['fname'] = html_entity_decode( stripslashes( $userarray[fname] ) );
        $userstats['lname'] = html_entity_decode( stripslashes( $userarray[lname] ) );
        $userstats['phone'] = stripslashes( $userarray[phone] );
        $userstats['email'] = stripslashes( $userarray[email] );
        $userstats['add1'] = html_entity_decode( stripslashes( $userarray[add1] ) );
        $userstats['add2'] = html_entity_decode( stripslashes( $userarray[add2] ) );
        $userstats['city'] = html_entity_decode( stripslashes( $userarray[city] ) );
        $userstats['rspchoice'] = $userarray['rspchoice'];
        if ( $userarray['rspchoice'] == "2" )
        {
            $userstats['province'] = $userarray['state'];
            $userstats['strStateValue'] = $userarray['state'];
        }
        else
        {
            $userstats['state'] = $userarray['state'];
            $userstats['strStateValue'] = $userarray['state'];
        }
        $userstats['zip'] = stripslashes( $userarray[zip] );
        $userstats['org'] = html_entity_decode( stripslashes( $userarray[org] ) );
        $userstats['country'] = stripslashes( $userarray[country] );
        $userstats['custom1'] = stripslashes( $userarray[custom1] );
        $userstats['custom2'] = stripslashes( $userarray[custom2] );
        $userstats['custom3'] = stripslashes( $userarray[custom3] );
    }
    return $userstats;
}

//
//
//echo "viewitem second";
//
//
$DIR = dirname( __FILE__ );
require( $DIR."/includes/dbconfig.php" );
include_once( WORKDIR."/includes/sessions.php" );
include( WORKDIR."/config.php" );
include( WORKDIR."/LoggedIn.php" );
define( ISAWIN, 1 );
if ( $action == "" ) {
    $action = $_GET['action'];
}
$iloggedin = false;
if ( $pkey && !$_REQUEST['pkey'] && !$_GET['pkey'] && !$_POST['pkey'] && !empty( $pkey ) && is_array( $thisinvoice ) )
{
    $pkey2 = md5( $vkey.$thisinvoice['ownerid'].md5( $thisinvoice['invoice_date']."idate1554NOW" ).$thisinvoice['id'].$base );
    if ( $pkey2 == $pkey )
    {
        $iloggedin = "1";
        $uid = $thisinvoice['id'];
        $newuid = $thisinvoice['ownerid'];
    }
}
if ( $LoggedIn != "1" && $action != "help" && $action != "whoislookup" && $action == "invoice" )
{
    $uid = mysql_real_escape_string( $uid );
    $thisinvoice = sqlarray( "select * from client_invoices where id='".$uid."'" );
    $vkey = md5( $thisinvoice['id'].$thisinvoice['ownerid'].md5( $thisinvoice['invoice_date']."idate1554NOW" )."AWBS".$base );
    $domainarr = array( id => $thisinvoice['id'], vkey => $vkey );
    $p_s_arr = serialize( $domainarr );
    $sendstr = urlencode( base64_encode( $p_s_arr ) );
    ob_start( );
    header( "Location: {$securebase}/aLogIn.php?subaction=".$this_filename."&id={$sendstr}" );
    exit( );
}
if ( $LoggedIn != "1" && $action != "help" && $action != "whoislookup" && !$iloggedin )
{
    ob_start( );
    header( "Location: {$base}" );
    @ob_end_flush( );
    exit( );
}
ob_start( );
$viewimage = "<img src=\"templates/{$templatedir}/images/binoc.jpg\" alt=\"\" />";
if ( $uid != "" )
{
    if ( $action != "help" && $action != "transfer_query" && $action != "whoislookup" && $action != "emailarchive" && !$iloggedin )
    {
        $seccheck = 1;
        //$seccheck = check_sec( $action, $uid, $newuid );
        if ( $seccheck <= 0 )
        {
            echo "<br /><br /><center><strong class=\"red\">System Error {$action} {$seccheck} {$uid} {$newuid} </strong>";
            exit( );
        }
    }
			    if(true)
                            {
                                if ( $action == "invoice" )
                                {
                                    $seclevel = "VPI";
                                    //include( WORKDIR."/includes/seccheck.php" );
                                    if ( !is_numeric( $uid ) ) {exit( );} else {$uid = mysql_real_escape_string( $uid );}
									$query 			= "SELECT * FROM client_invoices WHERE id='{$uid}'";
									$result 		= mysql_query( $query );
									$num 			= @mysql_numrows( @$result );
									$i 				= 0;
									$display_arr 	= array( );
									$edit_arr 		= array( );
									while ( $i < $num ) {
										$display_arr['ID'] 				= mysql_result( $result, $i, "id" );
										$itype 							= mysql_result( $result, $i, "i_type" );
										$display_arr['OwnerID'] 		= mysql_result( $result, $i, "ownerid" );
										$display_arr['OrderID'] 		= mysql_result( $result, $i, "seed" );
										$display_arr['Status'] 			= mysql_result( $result, $i, "status" );
										$display_arr['Description'] 	= mysql_result( $result, $i, "description" );
										$display_arr['InvoiceDate'] 	= mysql_result( $result, $i, "invoice_date" );
										if ( $display_arr['InvoiceDate'] != "" ) {
											$display_arr['InvoiceDate'] = strftime( $date_short, $display_arr['InvoiceDate'] );
										}
										$display_arr['DateDue'] 		= mysql_result( $result, $i, "date_due" );
										if ( $display_arr['DateDue'] != "" ) {
											$display_arr['DateDue'] 	= strftime( $date_short, $display_arr['DateDue'] );
										}
										$display_arr['AmountDue'] 		= mysql_result( $result, $i, "amount" );
										$display_arr['AmountPaid'] 		= mysql_result( $result, $i, "amount_paid" );
										$display_arr['AmountTax'] 		= mysql_result( $result, $i, "amount_tax" );
										$display_arr['DatePaid'] 		= mysql_result( $result, $i, "date_paid" );
										if ( $display_arr['DatePaid'] != "" && $display_arr['DatePaid'] != NULL )
										{
											$display_arr['DatePaid'] 	= strftime( $date_short, $display_arr['DatePaid'] );
										}
										$display_arr['SubTotal'] 		= $display_arr['AmountDue'] - $display_arr['AmountTax'];
										$display_arr['Balance'] 		= $display_arr['AmountDue'] - $display_arr['AmountPaid'];
										$display_arr['Balance'] 		= round( $display_arr['Balance'], $round_amount );
										$display_arr['Balance'] 		= LCURRENCYSYMBOL.sprintf( "{$rounding}", $display_arr['Balance'] ).RCURRENCYSYMBOL;
										$display_arr['SubTotal'] 		= round( $display_arr['SubTotal'], $round_amount );
										$display_arr['SubTotal'] 		= LCURRENCYSYMBOL.sprintf( "{$rounding}", $display_arr['SubTotal'] ).RCURRENCYSYMBOL;
										$display_arr['AmountDue'] 		= round( $display_arr['AmountDue'], $round_amount );
										$display_arr['AmountDue'] 		= LCURRENCYSYMBOL.sprintf( "{$rounding}", $display_arr['AmountDue'] ).RCURRENCYSYMBOL;
										$display_arr['AmountPaid'] 		= LCURRENCYSYMBOL.sprintf( "{$rounding}", $display_arr['AmountPaid'] ).RCURRENCYSYMBOL;
										$display_arr['AmountTax'] 		= round( $display_arr['AmountTax'], $round_amount );
										$display_arr['AmountTax'] 		= LCURRENCYSYMBOL.sprintf( "{$rounding}", $display_arr['AmountTax'] ).RCURRENCYSYMBOL;
										$display_arr['PayType'] 		= mysql_result( $result, $i, "pay_type" );
										if ( $display_arr['PayType'] == "Check" || $display_arr['PayType'] == "Queue" )	{
											$display_arr['PayType'] 	= CHECK;
										}
										$taxtypearr 					= array( );
										$taxtype 						= mysql_result( $result, $i, "tax_info" );
										$tax_info 						= str_replace( "|", "", $taxtype );
										$taxtypearr 					= explode( "=", $taxtype );
										$taxinfo 						= $taxtypearr[1];
										$tax_data 						= sqlarray( "select rate from tax_rules where id='{$taxinfo}' limit 1" );
										$real_tax_rate 					= $tax_data['rate'] * 100;
										
										define( 'TAXRATE', $real_tax_rate."%" );
										
										$real_tax_rate 					= $real_tax_rate."%";
										$display_arr['AuthCode'] 		= mysql_result( $result, $i, "auth_code" );
										$display_arr['PayHistory'] 		= mysql_result( $result, $i, "history" );
										$display_arr['PayHistory'] 		= str_replace( "\n", "<br />", $display_arr['PayHistory'] );
										$details 						= mysql_result( $result, $i, "domainlist" );
										$details 						= str_replace( "\n", "<br />", $details );
										$display_arr['Details'] 		= $details;
										$display_arr['PayHistory'] 		= wordwrap( $display_arr['PayHistory'], 60, "<br />" );
										$display_arr['Custom1'] 		= sqlsingle( "SELECT * FROM users WHERE username='{$newuid}'", "custom1" );
										$display_arr['Custom2'] 		= sqlsingle( "SELECT * FROM users WHERE username='{$newuid}'", "custom2" );
										$display_arr['Custom3'] 		= sqlsingle( "SELECT * FROM users WHERE username='{$newuid}'", "custom3" );
										++$i;
                                    }
									$cs = check_invoice( $display_arr['ID'] );
									if ( 0 < $cs[2] && $display_arr['Status'] != 1 && $display_arr['Status'] != 3 && $display_arr['InvoiceDate'] != "" ){
										$paid_image 					= "<img src=\"templates/{$templatedir}/images/pastdue.jpg\" alt=\"Past Due\" title=\"Past Due\" />";
									}
									else if ( $display_arr['Status'] == 1 )	{
										$paid_image 					= "<img src=\"templates/{$templatedir}/images/paid.jpg\" alt=\"Paid\" title=\"Paid\" />";
									}
									else if ( $display_arr['Status'] == 3 )	{
										$paid_image 					= "<img src=\"templates/{$templatedir}/images/void.jpg\" alt=\"Void\" title=\"Void\" />";
									}
									else {
										$paid_image 					= "";
									}
									$invrow 							= "<table class=\"invoiced width-100\"><tr><td class=\"tablehead\" width=\"10%\">".VID."</td><td class=\"tablehead\" width=\"5%\">".VDATE."</td><td class=\"tablehead\" width=\"5%\">".VAMOUNT."</td><td class=\"tablehead\">".VDESCRIPTION."</td></tr>";
									$tid 								= tcrypt( "goaway", $uid, "en" );
									//$query = mysql_query( $Tmp_2153."select * from client_invoices_items where invoiceid='{$tid}'" );
									$query 								= mysql_query( "select * from client_invoices_items where invoiceid='{$tid}'" );
									$invnum 							= 1;
									$realnum 							= 0;
									$dlcolor 							= "row1_11";
									$invoicearr 						= array( );
									reset( $invoicearr );
									$itemid 							= array( );
									reset( $itemid );
									$itemdate 							= array( );
									reset( $itemdate );
									$itemamount 						= array( );
									reset( $itemamount );
									$itemlist 							= array( );
									reset( $itemlist );
									$itemtaxcode 						= array( );
									reset( $itemtaxcode );
									while ( $row = mysql_fetch_array( $query, MYSQL_ASSOC ) ) {
										$dlcolor = $dlcolor == "row1_01" ? "row1_11" : "row1_01";
										
										$itemid[$invnum] 				= $row['id'];
										$invoicearr[$realnum]['id'] 	= $row['id'];
										$itemdate[$invnum] 				= $row['invoice_date'];
										$itemamount[$invnum] 			= $row['amount'];
										if ( $itemdate[$invnum] != "" )	{
											$itemdate[$invnum] 			= strftime( $date_short, $itemdate[$invnum] );
											$invoicearr[$realnum]['invoice_date'] = $itemdate[$invnum];
										}
										$itemlist[$invnum] 				= trim( $row['domainlist'] );
										$itemlist[$invnum] 				= str_replace( "\n", "<br />", $itemlist[$invnum] );
										if ( substr( $itemlist[$invnum], 0 - 6, strlen( $itemlist[$invnum] ) ) == "<br />" ) {
											$itemlist[$invnum] 			= substr( $itemlist[$invnum], 0, 0 - 6 );
										}
										if ( substr( $itemlist[$invnum], 0 - 4, strlen( $itemlist[$invnum] ) ) == "<br>" ) {
											$itemlist[$invnum] 			= substr( $itemlist[$invnum], 0, 0 - 4 );
										}
										$invoicearr[$realnum]['description'] 	= $itemlist[$invnum];
										$itemtaxcode[$invnum] 					= $row['tax_code'];
										$itemtaxcode[$invnum] 					= $itemtaxcode[$invnum] != "2" ? "" : "*";
										$invoicearr[$realnum]['tax_code'] 		= $itemtaxcode[$invnum];
										$itemamount[$invnum] 					= LCURRENCYSYMBOL.sprintf( "{$rounding}", $itemamount[$invnum] ).RCURRENCYSYMBOL;
										$invoicearr[$realnum]['amount'] 		= $itemamount[$invnum];
										$invrow 								.= "<tr><td>".$itemid[$invnum]."</td><td nowrap=\"nowrap\">".$itemdate[$invnum]."</td><td>".$itemamount[$invnum].$itemtaxcode[$invnum]."</td><td nowrap=\"nowrap\">".$itemlist[$invnum]."</td></tr>";
										
										//$xarray[] = $row;
										
										++$invnum;
										++$realnum;
									}
									$invrow 					.= "</table>";
									$display_arr['Details'] 	= $invrow;
									$transrow 					= "<table class=\"invoiced width-100\"><tr><td class=\"tablehead\" width=\"10%\">".VID."</td><td class=\"tablehead\" width=\"5%\">".VDATE."</td><td class=\"tablehead\" width=\"5%\">".VAMOUNT."</td><td class=\"tablehead\">".VDESCRIPTION."</td></tr>";
									$query 						= mysql_query( "select * from transactions where seed='{$uid}' and ownerid='".$display_arr['OwnerID']."'" );
									$numtransactions 			= mysql_numrows( $query );
									$invnum 					= 1;
									$realnum 					= 0;
									$dlcolor 					= "row1_11";
									$itemid 					= array( );
									reset( $itemid );
									$itemdate 					= array( );
									reset( $itemdate );
									$itemamount 				= array( );
									reset( $itemamount );
									$itemlist 					= array( );
									reset( $itemlist );
									$itemtaxcode 				= array( );
									reset( $itemtaxcode );
									while ( $row = mysql_fetch_array( $query, MYSQL_ASSOC ) ) {
										$dlcolor = $dlcolor == "row1_01" ? "row1_11" : "row1_01";

										$itemid[$invnum] 		= $row['id'];
										$transactionarr[$realnum]['id'] = $row['id'];
										$itemdate[$invnum] 		= $row['date'];
										$itemamount[$invnum] 	= $row['amount'];
										$itemlist[$invnum] 		= trim( $row['processor'] );
										$transactionarr[$realnum]['description'] = $itemlist[$invnum];
										if ( $itemdate[$invnum] != "" )	{
											$itemdate[$invnum] 	= strftime( $date_short, $itemdate[$invnum] );
											$transactionarr[$realnum]['date'] = $itemdate[$invnum];
										}
										$itemamount[$invnum] 	= LCURRENCYSYMBOL.sprintf( "{$rounding}", $itemamount[$invnum] ).RCURRENCYSYMBOL;
										$transactionarr[$realnum]['amount'] = $itemamount[$invnum];
										$transrow 				.= "<tr><td>".$itemid[$invnum]."</td><td nowrap=\"nowrap\">".$itemdate[$invnum]."</td><td>".$itemamount[$invnum].$itemtaxcode[$invnum]."</td><td nowrap=\"nowrap\">".$itemlist[$invnum]."</td></tr>";
										++$invnum;
										++$realnum;
									}
									$transrow 					.= "</table>";
									$display_arr['TransactionDetails'] = $transrow;
									$userstats 					= array( );
									$userstats 					= get_user_istats( $display_arr['OwnerID'], "", $display_arr['ID'] );
									$vviewer 					= 1;
									include( WORKDIR."/includes/gparser.php" );
									if ( $itype == "4" ) {
										define( 'INVSTRING', STOCKINVOICETITLE );
									}
									else {
										define( 'INVSTRING', INVOICETITLE );
									}
									
									/* *********** SZÁMLÁZZ MODUL - Csőglei BEGIN */
									if($_GET['flag']==1) {
										require_once("szamlazz/invoiceAgent.class.php");
										$szamlazz = new invoiceAgent('/var/www/html/awbs/szamlazz/invoiceAgent.ini');
										
										echo "<pre>";
										print_r($display_arr);
										echo "</pre>";
																				
										echo "<pre>";
										print_r($invoicearr);
										echo "</pre>";
										
										echo "<pre>";
										print_r($userstats);
										echo "</pre>";

		
										$settings = [
											"username" => "csogleig@gmail.com",     // username, required, plain string
											"password" => "Csogi001",     // user password, required, plain string
											"e_invoice" => false,            // use true or false, without quotes
											"keychain" => "", 
											"download_invoice" => true,
											"download_count" => 1           // use whole number
										];									
										$today 	= new DateTime('NOW');
										$xpaid 	= $display_arr['Status']==1 ? true : false;
										$header = [
											"invoice_date" => $today, // set date in YYYY-MM-DD format
											"fulfillment" => "2019-04-30",
											"payment_due" => "2019-04-30",
											"payment_method" => $display_arr['PayType'],
											"currency" => "USD",
											"language" => "hu",
											"comment" => "",
											"exchange_bank" => "",
											"exchange_rate" => "0.0",
											"order_no" => $display_arr['OrderID'],
											"is_deposit" => false,
											"is_final" => false,
											"is_proform" => false,
											"num_prefix" => "CSGL",
											"is_paid" => $xpaid 
										];
										$seller = [
											"bank" => "OTP Bank",
											"bank_account" => "11111111-22222222-33333333",
											"email_replyto" => "hello@hello.hu",
											"email_subject" => "Számla értesítő",
											"email_content" => ""
										];
										$buyer = [
											"name" => "Kovács Bt.",
											"zip" => "2030",
											"city" => "Érd",
											"address" => "Tárnoki út 23.",
											"email" => "vevoneve@example.org",
											"send_email" => true,
											"tax_no" => "11111111-1-11",
											"postal_name" => "Kovács Bt. postázási név",
											"postal_zip" => "2040",
											"postal_city" => "Budaörs",
											"postal_address" => "Szivárvány utca 8. VI.em. 82.",
											// xml branch how to: 
											// "buyer_account" => ["account_date" => "2015-12-12", "buyer_id" => "123456", "account_id" => "123456"], 
											"signatory" => "Vevő Aláírója",
											"phone" => "+3630-555-55-55, Fax:+3623-555-555",
											"comment" => "A portáról felszólni a 214-es mellékre."
										];
										$szamlazz->addItem([
											"name" => "Eladó izé 1",
											"quantity" => "1.0",
											"quantity_unit" => "db",
											"unit_price" => "10000",
											"vat" => "27",
											"net_price" => "10000.0",
											"vat_amount" => "2700.0",
											"gross_amount" => "12700.0",
											"comment" => "tétel megjegyzés 1"
										]);
										$response = $szamlazz->_generateInvoice($settings, $header, $seller, $buyer, null);   
										if ($response) {
											echo "sikerült";
										} else {
											echo "nem sikerült";
										}
										
										unset($szamlazz);
									} /* *********** SZÁMLÁZZ MODUL - Csőglei  END */
									else {
										$template->set_var( "itype", $itype );
										$template->set_var( "allowpdf", $allowpdf );
										$template->set_var( "numtransactions", $numtransactions );
										$template->set_var( "invoicearr", $invoicearr );
										$template->set_var( "transactionarr", $transactionarr );
										$template->set_var( "paid_image", $paid_image );
										$template->set_var( "dmsg", $dmsg );
										$template->set_var( "display_arr", $display_arr );
										$template->set_var( "userstats", $userstats );
										print $template->parse( "vinvoice.php" );
									}
									
									@ob_end_flush( );
									exit( );
                                }
                            //}
                        //}
                    //}
                //}
            //}
        //}
    }
}
else
{
    $viewerlist = "<tr><td align=\"center\"><br /><br /><center><strong class=\"red\">System Error</strong></td></tr>";
}
$header = "<head>\r\n<title>System Viewer</title>\r\n<META HTTP-EQUIV=\"Pragma\" content=\"no-cache\">\r\n<META HTTP-EQUIV=\"Cache-Control\" content=\"no-cache\">\r\n<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/{$templatedir}/css/style.css\"></head>";
$vviewer = 1;
include( WORKDIR."/includes/gparser.php" );
$template->set_var( "ccprocessor", $ccprocessor );
$template->set_var( "header", $header );
$template->set_var( "viewimage", $viewimage );
$template->set_var( "viewerlist", $viewerlist );
$template->set_var( "dmsg", $dmsg );
$template->set_var( "display_arr", $display_arr );
$template->set_var( "userstats", $userstats );
if ( $maintmode == "True" )
{
    print $template->parse( "maintmode.php" );
    exit( );
}
print $template->parse( "viewitem.php" );
@ob_end_flush( );
?>
