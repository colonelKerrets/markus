<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "hello@tanjaoberst.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "592b45" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'02A6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeklEQVR4nM2QLQ6AMAxGO1GPGPfpBL5LmACP4RRFcINxhJmdksk2ICHQz73056VQLyXwp7zi58iNkOEgxZBxhwTMivnstxACdYrxDtsgkbTfXGopdVpW5df6Mko0+xpjTJG8ueGo9RnWXASFzayjPg3Cxvmr/z2YG78Te+XL2cio9gIAAAAASUVORK5CYII=',
			'A134' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhDGRoCkMRYAxgDWBsdGpHFRKawBgBVtSKLBbQyBDA0OkwJQHJf1NJVUaumroqKQnIfRJ2jA7Le0FCgWENgaAi6eUCXoNsBdAuaGGsoupsHKvyoCLG4DwDbb80LYQ4GEQAAAABJRU5ErkJggg==',
			'6EAB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQxmmMIY6IImJTBFpYAhldAhAEgtoEWlgdHR0EEEWaxBpYG0IhKkDOykyamrY0lWRoVlI7guZgqIOorcVKBYaiGpeK0SdCJpb0PWC3AwUQ3HzQIUfFSEW9wEAtijMDXrjJE4AAAAASUVORK5CYII=',
			'17B8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB1EQ11DGaY6IImxOjA0ujY6BAQgiYmCxBoCHURQ9DK0siLUgZ20MmvVtKWhq6ZmIbkPqC6AFc08RgdGB1YM81gbMMVEGtD1ioYAxdDcPFDhR0WIxX0AxjrKLbK8wGsAAAAASUVORK5CYII=',
			'12E8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHaY6IImxOrC2sjYwBAQgiYk6iDS6AlWLoOhlAIrB1YGdtDJr1dKloaumZiG5D6huCrp5QLEAVgzzGB0wxVgbMNwSIhrqiubmgQo/KkIs7gMAwz3Ioq53RFUAAAAASUVORK5CYII=',
			'C734' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QsRGAMAhFSZEN4j6ksMe7pMk0UGQDdQimNFoFtdRTfvc/3H8H6GUY/qRX+HwacszA1HmhgoyC0nskIMhUjcfQNnGmjq+orrpoKR1fywkkor11CDzlZDr8kViWwH5vNsyB3Yn5q/89qBu+DSaxz0DSPR8YAAAAAElFTkSuQmCC',
			'E3F5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDA0MDkMQCGkRaWRsYHRhQxBgaXTHFQOpcHZDcFxq1Kmxp6MqoKCT3QdQxNIhgmIdNjNEBVQzkFoYAZPeB3dzAMNVhEIQfFSEW9wEAuJjL9uDBq2gAAAAASUVORK5CYII=',
			'6D6B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WANEQxhCGUMdkMREpoi0Mjo6OgQgiQW0iDS6Njg6iCCLNYDEGGHqwE6KjJq2MnXqytAsJPeFTAGqQzevFaQ3ENU8LGLY3ILNzQMVflSEWNwHAJtIzJNV7CS9AAAAAElFTkSuQmCC',
			'BA26' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGaY6IIkFTGEMYXR0CAhAFmtlbWVtCHQQQFEn0ugAFEN2X2jUtJVZKzNTs5DcB1bXyohmnmiowxRGBxEUMaC6ADQxoF5HBwYUvaEBIo2uoQEobh6o8KMixOI+AHgfzXaIXn0JAAAAAElFTkSuQmCC',
			'56EB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDHUMdkMQCGlhbWRsYHQJQxEQaQWIiSGKBASINSOrATgqbNi1saejK0Cxk97WKYpjH0CrS6IpmXgAWMZEpmG5hDcB080CFHxUhFvcBAHfIyrz3NqJkAAAAAElFTkSuQmCC',
			'3486' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7RAMYWhlCGaY6IIkFTGGYyujoEBCArBKoirUh0EEAWWwKoyujo6MDsvtWRi1duip0ZWoWsvumiLQC1aGZJxrqCjRPBNWOVlY0MaBbWtHdgs3NAxV+VIRY3AcAX5DK10c0YG4AAAAASUVORK5CYII=',
			'DAD6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYAlhDGaY6IIkFTGEMYW10CAhAFmtlbWVtCHQQQBETaXQFiiG7L2rptJWpqyJTs5DcB1WHZp5oKEivCBbzUMSmAMXQ3BIaABRDc/NAhR8VIRb3AQCEhc8tHqYBKwAAAABJRU5ErkJggg==',
			'FFC5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNFQx1CHUMDkMQCGkQaGB0CHRjQxFgbBLGIMbo6ILkvNGpq2NJVK6OikNwHUccAJNH1YhMTdEAXY3QICEB3H0Oow1SHQRB+VIRY3AcAj9LMqhg2MKMAAAAASUVORK5CYII=',
			'3DA4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RANEQximMDQEIIkFTBFpZQhlaEQWY2gVaXR0dGhFEZsi0ugKVB2A5L6VUdNWpq6KiopCdh9YXaADunmuoYGhIehiQJegu4UVTQzkZnSxgQo/KkIs7gMA3zPPY6CNi3oAAAAASUVORK5CYII=',
			'C002' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WEMYAhimMEx1QBITaWUMYQhlCAhAEgtoZG1ldHR0EEEWaxBpdAWRSO6LWjVtZSqQjEJyH1RdowOm3lYGDDscpjBgcQummxlDQwZB+FERYnEfAGszzFUX23JgAAAAAElFTkSuQmCC',
			'5F2B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGUMdkMQCGkQaGB0dHQLQxFgbAh1EkMQCA0C8QJg6sJPCpk0NW7UyMzQL2X2tQHWtjCjmgcWmMKKYFwASC0AVE5kCdIsDql5WoL2soYEobh6o8KMixOI+AOW3yuxokxlSAAAAAElFTkSuQmCC',
			'77C9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkNFQx1CHaY6IIu2MjQ6OgQEBKCJuTYIOoggi01haGVtYISJQdwUtWraUiAZhuQ+RgeGANYGhqnIekH6gGINyGIiQFHWBgEUOwKAooxobgGJMaC7eYDCj4oQi/sAa2jLk7Bm4h4AAAAASUVORK5CYII=',
			'FD62' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGaY6IIkFNIi0Mjo6BASgijW6Njg6iGCIMTSIILkvNGraytSpq1ZFIbkPrM7RodEBQ29AKwOm2BQGLG5BFQO5mTE0ZBCEHxUhFvcBACQ7zpJfx5gGAAAAAElFTkSuQmCC',
			'1393' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB1YQxhCGUIdkMRYHURaGR0dHQKQxEQdGBpdGwIaRFD0MrSyAsUCkNy3MmtV2MrMqKVZSO4DqWMIgauDiTU6YJrX6IghhsUtIZhuHqjwoyLE4j4AJyXJ2EOHAAkAAAAASUVORK5CYII=',
			'30C3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7RAMYAhhCHUIdkMQCpjCGMDoEOgQgq2xlbWVtEGgQQRabItLoClKP5L6VUdNWpq5atTQL2X2o6qDmQcRECNiBzS3Y3DxQ4UdFiMV9ACSlzCgTy/XeAAAAAElFTkSuQmCC',
			'DA17' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgMYAhimMIaGIIkFTGEMYQhhaBBBFmtlbWXEEBNpdJgCpJHcF7V02sqsaatWZiG5D6qulQFFr2goUGwKA6Z5AShiU0BijA6obhZpdAx1RBEbqPCjIsTiPgAF483HKy5S8AAAAABJRU5ErkJggg==',
			'9B33' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WANEQxhDGUIdkMREpoi0sjY6OgQgiQW0ijQ6NAQ0iKCKtTKARRHumzZ1atiqqauWZiG5j9UVRR0EYjFPAIsYNrdgc/NAhR8VIRb3AQBiBM3ob2vM5wAAAABJRU5ErkJggg==',
			'35E9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7RANEQ1lDHaY6IIkFTBFpYG1gCAhAVtkKEmN0EEEWmyISgiQGdtLKqKlLl4auigpDdt8UhkbXBoapKHpbwWINqGIiIDEUOwKmsLaiu0U0gDEE3c0DFX5UhFjcBwD/GMtRdRqcyQAAAABJRU5ErkJggg==',
			'F5C1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkNFQxlCHVqRxQIaRBoYHQKmoouxNgiEoomFsDYwwPSCnRQaNXXp0lWrliK7L6CBodEVoQ6PmAhQTABNjLUV6BY0McYQoJtDAwZB+FERYnEfALaZzYiLkoY2AAAAAElFTkSuQmCC',
			'0342' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB1YQxgaHaY6IImxBoi0MrQ6BAQgiYlMAalydBBBEgsAqmIIdGgQQXJf1NJVYSszs1ZFIbkPpI610aHRAVVvo2sokES3o9FhCgO6WxodAjDd7BgaMgjCj4oQi/sA8LzMx0DtFDsAAAAASUVORK5CYII=',
			'3D5E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7RANEQ1hDHUMDkMQCpoi0sjYwOqCobBVpdEUXmwIUmwoXAztpZdS0lamZmaFZyO4DqnNoCMQwD5uYK5oYyC2Mjo4oYiA3M4Qyorh5oMKPihCL+wAv8MqWj7isxAAAAABJRU5ErkJggg==',
			'866B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMdkMREprC2Mjo6OgQgiQW0ijSyNjg6iKCoE2lgbWCEqQM7aWnUtLClU1eGZiG5T2SKaCsrFvNcGwJRzMMmhs0t2Nw8UOFHRYjFfQAMSstjmlFsmAAAAABJRU5ErkJggg==',
			'BD79' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDA6Y6IIkFTBFpBZIBAchirSKNDg2BDiKo6hodGh1hYmAnhUZNW5m1dFVUGJL7wOqmMEwVQTcvgKEBXczRgQHdjlbWBgYUt4Dd3MCA4uaBCj8qQizuAwBaEc6HLG6FJQAAAABJRU5ErkJggg==',
			'A19C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGaYGIImxBjAGMDo6BIggiYlMYQ1gbQh0YEESC2hlAIshuy9q6aqolZmRWcjuA6ljCIGrA8PQUKBYA6oYSB0jFjvQ3RLQyhqK7uaBCj8qQizuAwAfYsk6oGQJVAAAAABJRU5ErkJggg==',
			'FD3A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNFQxhDGVqRxQIaRFpZGx2mOqCKNTo0BAQEoIs1OjqIILkvNGrayqypK7OmIbkPTR2SeYGhIZhi6OqAbkHXC3IzI4rYQIUfFSEW9wEAdGnOrzMrotAAAAAASUVORK5CYII=',
			'0B50' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDHVqRxVgDRFpZGximOiCJiUwRaXRtYAgIQBILaAWqm8roIILkvqilU8OWZmZmTUNyH0gdQ0MgTB1MrNEBTQxiRwCKHSC3MDo6oLgF5GaGUAYUNw9U+FERYnEfALMMy9mOoH9mAAAAAElFTkSuQmCC',
			'D1DE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVUlEQVR4nGNYhQEaGAYTpIn7QgMYAlhDGUMDkMQCpjAGsDY6OiCrC2hlDWBtCEQTY0AWAzspaikIRYZmIbkPTR1pYlMYMNwSCnQxupsHKvyoCLG4DwDTxMpMsydeIAAAAABJRU5ErkJggg==',
			'24E9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYWllDHaY6IImJTGGYytrAEBCAJBbQyhDK2sDoIIKsu5XRFUkM4qZpS5cuDV0VFYbsvgCRVqB5U5H1MjqIhrqC7EJ2C9BEIEaxQwQihuKW0FBMNw9U+FERYnEfAMYbyle2xGsWAAAAAElFTkSuQmCC',
			'B9EC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDHaYGIIkFTGFtZW1gCBBBFmsVaXRtYHRgQVEHEUN2X2jU0qWpoSuzkN0XMIUxEEkd1DyGRkwxFix2YLoFm5sHKvyoCLG4DwBbVcxKsO1H6wAAAABJRU5ErkJggg==',
			'BBE8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUElEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDHaY6IIkFTBFpZW1gCAhAFmsVaXRtYHQQwa0O7KTQqKlhS0NXTc1Cch/R5hG2A6ebByr8qAixuA8A7OjNlnJg6gQAAAAASUVORK5CYII=',
			'A82F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUNDkMRYA1hbGR0dHZDViUwRaXRtCEQRC2hlbWVAiIGdFLV0ZdiqlZmhWUjuA6trZUTRGxoq0ugwhRHNPKBYALoY0C0O6GKMIayhqG4ZqPCjIsTiPgDh6smoi7stsQAAAABJRU5ErkJggg==',
			'CA6C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WEMYAhhCGaYGIImJtDKGMDo6BIggiQU0srayNjg6sCCLNYg0ujYwOiC7L2rVtJWpU1dmIbsPrM7R0YEBRa9oqGtDIKpYI8i8QBQ7RFpFGh3R3MIaItLogObmgQo/KkIs7gMA4qDMRFxatNUAAAAASUVORK5CYII=',
			'C189' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WEMYAhhCGaY6IImJtDIGMDo6BAQgiQU0sgawNgQ6iCCLNTAA1TnCxMBOigKh0FVRYUjug6hzmIqulxVIoog1gsVQ7BBpZcBwC2sIayi6mwcq/KgIsbgPACYZydg6I2xiAAAAAElFTkSuQmCC',
			'431F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpI37prCGMExhDA1BFgsRaWUIYXRAVscYwtDoiCbGOoWhFagXJgZ20rRpq8JWTVsZmoXkvgBUdWAYGsrQ6IAmxjAFm5gIhl6QmxlDHVHFBir8qAexuA8A5/XI2Z3wIjgAAAAASUVORK5CYII=',
			'DFA0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgNEQx2mMLQiiwVMEWlgCGWY6oAs1irSwOjoEBCAJsbaEOggguS+qKVTw5auisyahuQ+NHUIsVAsYg0BqHZMAYuhuCU0ACyG4uaBCj8qQizuAwDrS86TcA45UAAAAABJRU5ErkJggg==',
			'C1AA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WEMYAhimMLQii4m0MgYwhDJMdUASC2hkDWB0dAgIQBZrYAhgbQh0EEFyXxQQLV0VmTUNyX1o6hBioYGhISh2YKoTacUUYw1hDUUXG6jwoyLE4j4A02HKXC8Z1QkAAAAASUVORK5CYII=',
			'249A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WAMYWhlCgRhJTGQKw1RGR4epDkhiAUBVrA0BAQHIulsZXVkbAh1EkN03benSlZmRWdOQ3Rcg0soQAlcHhowOoqEODYGhIchuAZnYgKpOBCTm6IgiFgpybygjithAhR8VIRb3AQAQMMpcKhAc5AAAAABJRU5ErkJggg==',
			'282E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMDkMREprC2Mjo6OiCrC2gVaXRtCEQRY2hlbWVAiEHcNG1l2KqVmaFZyO4LAKprZUTRy+gg0ugwBVWMtQEoFoAqJtIAdIsDqlhoKGMIa2ggipsHKvyoCLG4DwAj/cjkjBxTxgAAAABJRU5ErkJggg==',
			'B33D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgNYQxhDGUMdkMQCpoi0sjY6OgQgi7UyNDo0BDqIoKhjAIo6wsTATgqNWhW2aurKrGlI7kNTh9s8rHZgugWbmwcq/KgIsbgPANvjzY0YyEKCAAAAAElFTkSuQmCC',
			'E473' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYWllDA0IdkMQCGhimMjQEOgSgioWCSBEUMUZXhkaHhgAk94VGLV26CgizkNwH1NXKMIWhAdU80VCHAAY08xhaGR0wxVgbGFHcAnYz0D3Ibh6o8KMixOI+AFSvzbfom+4sAAAAAElFTkSuQmCC',
			'395D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7RAMYQ1hDHUMdkMQCprC2sjYwOgQgq2wVaXQFiokgi00Bik2Fi4GdtDJq6dLUzMysacjum8IY6NAQiKq3laERU4wFaAeqGMgtjI6OKG4BuZkhlBHFzQMVflSEWNwHANhnyxmO2YboAAAAAElFTkSuQmCC',
			'5DF1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDA1qRxQIaRFpZGximook1ujYwhCKLBQaAxWB6wU4KmzZtZWroqqUo7mtFUYdTLACLmMgUsFtQxFgDgG4GuiVgEIQfFSEW9wEA3RbMm1OPGaMAAAAASUVORK5CYII=',
			'5575' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDA0MDkMQCGkSAZKADAwGxwACREIZGR1cHJPeFTZu6dNXSlVFRyO5rZWh0mMIANoEBWSwAVSygVaTR0YHRAVlMZAprK2sDQwCy+1gDGEOAYlMdBkH4URFicR8AElrL7aPtDvAAAAAASUVORK5CYII=',
			'D75F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgNEQ11DHUNDkMQCpjA0ujYwOiCrC2jFKtbKOhUuBnZS1NJV05ZmZoZmIbkPqC6AoSEQTS9IH7oYawMrutgUkQZGR0cUsdAAkQaGUFS3DFT4URFicR8AO8nLJnz6qJAAAAAASUVORK5CYII=',
			'0D91' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGVqRxVgDRFoZHR2mIouJTBFpdG0ICEUWC2gFi8H0gp0UtXTayszMqKXI7gOpcwgJaEXX69CAKgaywxFNDOoWFDGom0MDBkH4URFicR8AbdbMdmcA1vAAAAAASUVORK5CYII=',
			'476F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpI37poiGOoQyhoYgi4UwNDo6Ojogq2MEirk2oIqxTmFoZW1ghImBnTRt2qppS6euDM1Ccl/AFIYAVjTzQkMZHVgbAh1Q3cLagCkm0sCIphckxhDKiCo2UOFHPYjFfQCvQslCUX8PxAAAAABJRU5ErkJggg==',
			'21BB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGUMdkMREpjAGsDY6OgQgiQW0sgawNgQ6iCDrbmVAVgdx07RVUUtDV4ZmIbsvgAHDPEYHBgzzWBswxUQaMPWGhrKGort5oMKPihCL+wBzjck26+UeDgAAAABJRU5ErkJggg==',
			'F2B3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDGUIdkMQCGlhbWRsdHQJQxEQaXUEkihhDo2ujQ0MAkvtCo1YtXRq6amkWkvuA8lNYEepgYgGsGOYxOmCKsTZgukU01BXNzQMVflSEWNwHAIkOzuKTWdCiAAAAAElFTkSuQmCC',
			'7E70' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDA1pRRFtFgGTAVAdMsYAAZLEpQLFGRwcRZPdFTQ1btXRl1jQk9zGCVExhhKkDQ9YGIC8AVUwECBkdGFDsCACKsTYwoLgloAHoZqCLBkP4URFicR8AfvzLgWmxWucAAAAASUVORK5CYII=',
			'8CC7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYQxlCHUNDkMREprA2OjoENIggiQW0ijS4NgigiIlMEWlgBckhuW9p1LRVS1etWpmF5D6oulYGNPOAYlPQxYB2BDBguCXQAYubUcQGKvyoCLG4DwDzCcyNxj3a+AAAAABJRU5ErkJggg==',
			'CEBD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WENEQ1lDGUMdkMREWkUaWBsdHQKQxAIagWINgQ4iyGINEHUiSO6LWjU1bGnoyqxpSO5DU4cQQzcPix3Y3ILNzQMVflSEWNwHAN+ly/VkuoBKAAAAAElFTkSuQmCC',
			'1B51' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDHVqRxVgdRFpZGximIouJOog0ujYwhKLqBaqbygDTC3bSyqypYUszs5Yiuw+kjqEhoBVNb6MDFjFXTLFWRkdU94mGiIYAXRIaMAjCj4oQi/sAsX3JhZXwLBQAAAAASUVORK5CYII=',
			'211F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WAMYAhimMIaGIImJTGEMYAhhdEBWF9DKGsCIJsbQCtYLE4O4adqqqFXTVoZmIbsvAEUdGAJ5GGKsDZhiIljEQkNZQxlDHVHdMkDhR0WIxX0AuDrGK4wLWpkAAAAASUVORK5CYII=',
			'0DD1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGVqRxVgDRFpZGx2mIouJTBFpdG0ICEUWC2gFi8H0gp0UtXTaylQgiew+NHU4xaB2YHMLihjUzaEBgyD8qAixuA8At1LNf3w2K+sAAAAASUVORK5CYII=',
			'2CA0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMLQii4lMYW10CGWY6oAkFtAq0uDo6BAQgKwbKMbaEOggguy+adNWLV0VmTUN2X0BKOrAkBHIYw1FFWNtEGlwbQhAsQOoqhEohuKW0FDGUFagiwZD+FERYnEfANKxzOvcdqbsAAAAAElFTkSuQmCC',
			'2300' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WANYQximMLQii4lMEWllCGWY6oAkFtDK0Ojo6BAQgKy7laGVtSHQQQTZfdNWhS1dFZk1Ddl9ASjqwJDRgaHRFU2MtQHTDpEGTLeEhmK6eaDCj4oQi/sAFqzLV1jwqMoAAAAASUVORK5CYII=',
			'1030' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhDGVqRxVgdGENYGx2mOiCJiTqwAtUEBASg6BVpdGh0dBBBct/KrGkrs6YCSST3oalDiDUEoolhswOLW0Iw3TxQ4UdFiMV9ABY/ych8pqNnAAAAAElFTkSuQmCC',
			'8F1D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WANEQx2mMIY6IImJTBFpYAhhdAhAEgtoFWlgBIqJoKubAhcDO2lp1NSwVdNWZk1Dch+aOrh5xIjB9CK7hTUA6JZQRxQ3D1T4URFicR8AchjK399cVuYAAAAASUVORK5CYII=',
			'E338' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QkNYQxhDGaY6IIkFNIi0sjY6BASgiDE0OjQEOoigirUyINSBnRQatSps1dRVU7OQ3IemDp95WMQw3YLNzQMVflSEWNwHAO2ozmj++bo4AAAAAElFTkSuQmCC',
			'9F98' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQx1CGaY6IImJTBFpYHR0CAhAEgtoFWlgbQh0EMEQC4CpAztp2tSpYSszo6ZmIbmP1RWoKyQAxTyGVpBJqOYJAMUY0cSwuYU1AKgCzc0DFX5UhFjcBwDxK8vkwpOqrgAAAABJRU5ErkJggg==',
			'DC7E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1lDA0MDkMQCprA2OjQEOiCrC2gVacAmxtDoCBMDOylq6bRVq5auDM1Cch9Y3RRGTL0BmGKODmhiQLe4NqCKgd3cwIji5oEKPypCLO4DAMkfzGVp4eKOAAAAAElFTkSuQmCC',
			'0466' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YWhlCGaY6IImxBjBMZXR0CAhAEhOZwhDK2uDoIIAkFtDK6MoKMgHJfVFLgWDqytQsJPcFtIq0sjo6opgX0Coa6toQ6CCCakcrK5oY0C2t6G7B5uaBCj8qQizuAwAxssq3PHyn0gAAAABJRU5ErkJggg==',
			'5A5B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7QkMYAlhDHUMdkMQCGhhDWBsYHQJQxFhbQWIiSGKBASKNrlPh6sBOCps2bWVqZmZoFrL7WkUaHRoCUcxjaBUNBYkhmxcAVOeKJiYyRaTR0dERRS8r0F6HUEYUNw9U+FERYnEfAPmvzC3zHfwPAAAAAElFTkSuQmCC',
			'09B2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGaY6IImxBrC2sjY6BAQgiYlMEWl0bQh0EEESC2gFijU6NIgguS9q6dKlqaFAGsl9Aa2MgUB1jQ4oehmA5gFJFDtYQGJTGLC4BdPNjKEhgyD8qAixuA8AM6/M9F650uIAAAAASUVORK5CYII=',
			'7DCD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNFQxhCHUMdkEVbRVoZHQIdAlDFGl0bBB1EkMWmgMQYYWIQN0VNW5m6amXWNCT3AVUgqwND1gZMMZEGTDsCGjDdEtCAxc0DFH5UhFjcBwByf8ux+V6VHAAAAABJRU5ErkJggg==',
			'B2CC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgMYQxhCHaYGIIkFTGFtZXQICBBBFmsVaXRtEHRgQVHHABRjdEB2X2jUqqVLV63MQnYfUN0UVoQ6qHkMAZhijA6sGHaAVKG6JTRANNQBzc0DFX5UhFjcBwB9Kcxb1sZWUgAAAABJRU5ErkJggg==',
			'6502' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2QsQ2AMAwEP0U2MPtAQe8iKWAaRyIbmBFoMiWBBkdQgoRf+uKaPxnldoI/5RM/z12EYu0NIyVBBLNhvJC4YejJMqHgjzZ+07xuW5lrLr+gSKNwshucT5bRMEp1QtG4+Hy4tM4uQF0MP/jfi3nw2wHxU8zMDyHDOQAAAABJRU5ErkJggg==',
			'AB7C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDA6YGIImxBoi0MjQEBIggiYlMEWl0aAh0YEESC2gFqmt0dEB2X9TSqWGrlq7MQnYfWN0URgdke0NDgeYFoIoB1QFNY8Swg7WBAcUtAa1ANzcwoLh5oMKPihCL+wAI9swg889tXQAAAABJRU5ErkJggg==',
			'0942' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHaY6IImxBrC2MrQ6BAQgiYlMEQGqcnQQQRILaAWKBTo0iCC5L2rp0qWZmVmropDcF9DKGOja6NDogKKXodE1FEii2MECUjWFAd0tjQ4BmG52DA0ZBOFHRYjFfQBsiM0gG8+UBQAAAABJRU5ErkJggg==',
			'B37D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgNYQ1hDA0MdkMQCpoi0MjQEOgQgi7UyNDoAxURQ1DEARR1hYmAnhUatClu1dGXWNCT3gdVNYUTVCzIvAFPM0QFNDOgW1gZGFLeA3dzAiOLmgQo/KkIs7gMAFFvMrW4WnrQAAAAASUVORK5CYII=',
			'5415' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QkMYWhmmMIYGIIkB2VMZQhgdGFDFQhnRxAIDGF2Bel0dkNwXNm3p0lXTVkZFIbuvVQRoB0ODCLLNraKhDmhiAa1gtzggi4lMAYkxBCC7jzWAoZUx1GGqwyAIPypCLO4DADJYysJjhzHtAAAAAElFTkSuQmCC',
			'A69D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMdkMRYA1hbGR0dHQKQxESmiDSyNgQ6iCCJBbSKNCCJgZ0UtXRa2MrMyKxpSO4LaBVtZQhB1RsaKtLogGleoyOGGKZbAlox3TxQ4UdFiMV9AM5fy3GBgxJ4AAAAAElFTkSuQmCC',
			'001A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB0YAhimMLQii7EGMIYwhDBMdUASE5nC2goUDQhAEgtoFWl0mMLoIILkvqil01ZmgRCS+9DUIYuFhqDZwYCmDuwWNDGQmxlDHVHEBir8qAixuA8AmrbKDhwL8SMAAAAASUVORK5CYII=',
			'5454' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7QkMYWllDHRoCkMSA7KmsDQyNaGKhQLFWZLHAAEZX1qkMUwKQ3Bc2benSpZlZUVHI7msVaQWqdkDWy9AqCrQ1MDQE2Y5WoFuANiGrE5nC0MroiOo+1gCGVoZQBhSxgQo/KkIs7gMA9HHNien6WyMAAAAASUVORK5CYII=',
			'B4A5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM2QsRHAIAhFsXADs48W9lhQxGlo3ABHsHHKaIdJyuROfvfuw70D+mMYdsovfoRQQAyhYihQgYzXPSyDhLAyMdFyil75UW6t9TNn5YfiimVkt9w7KNKdwegl71aXuYvabzoPVv0G//swL34XHT/NXht3dG8AAAAASUVORK5CYII=',
			'4EC1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpI37poiGMoQ6tKKIhYg0MDoETEUWYwSKsTYIhCKLsU4BiTHA9IKdNG3a1LClq1YtRXZfAKo6MAwNxRRjAKsTwBADugVNDOzm0IDBEH7Ug1jcBwD2oMtch3UkYAAAAABJRU5ErkJggg==',
			'4BD0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpI37poiGsIYytKKIhYi0sjY6THVAEmMMEWl0bQgICEASY50CVNcQ6CCC5L5p06aGLV0VmTUNyX0BqOrAMDQUZB6qGMMUTDsYpmC6BaubByr8qAexuA8ATM7NO+pvfCoAAAAASUVORK5CYII=',
			'0CA2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YQxmmMEx1QBJjDWBtdAhlCAhAEhOZItLg6OjoIIIkFtAq0sDaENAgguS+qKXTVi1dFQWECPdB1TU6oOsNDWhlQLPDtSFgCgOaW4BiAehuZm0IDA0ZBOFHRYjFfQBJq80+2Zs/qgAAAABJRU5ErkJggg==',
			'37FF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7RANEQ11DA0NDkMQCpjA0ujYwOqCobMUiNoWhlRUhBnbSyqhV05aGrgzNQnbfFIYAVgzzGB0wxVgb0MUCpohgiIkGYIoNVPhREWJxHwAwDMislQSC1gAAAABJRU5ErkJggg==',
			'50A9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM3QsQ2AIBCF4aNgAwbSwv6RgAW9e5wFGwA76JQqickRLTXKdV+O8AdaL4fpT/NKn3cESpQ7YWDlyBPQmI6q7zsjzMLMA9vTatJYyjKtIYyyLx57yPJuNQ+WhqijZjRvmKTcbk2LBmG3pvmr/3twbvo2fWzMlS4vsHUAAAAASUVORK5CYII=',
			'D04D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgMYAhgaHUMdkMQCpjCGMLQ6OgQgi7WytjJMdXQQQRETaXQIhIuBnRS1dNrKzMzMrGlI7gOpc23E1OsaGogmBrQDXR3ILY2obsHm5oEKPypCLO4DAL+yzXLorFTNAAAAAElFTkSuQmCC',
			'E991' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGVqRxQIaWFsZHR2mooqJNLo2BIRiEYPpBTspNGrp0szMqKXI7gtoYAx0CAlAs4Oh0aEBXYyl0RFDDOwWFDGom0MDBkH4URFicR8A04HNi2nPEKIAAAAASUVORK5CYII=',
			'A92F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUNDkMRYA1hbGR0dHZDViUwRaXRtCEQRC2gVaXRAiIGdFLV06dKslZmhWUjuC2hlDHRoZUTRGxrK0OgwhRHNPJZGhwB0MaBbHNDFGENYQ1HdMlDhR0WIxX0AOLbJ4pezWGgAAAAASUVORK5CYII=',
			'F069' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGaY6IIkFNDCGMDo6BASgiLG2sjY4OoigiIk0ujYwwsTATgqNmrYydeqqqDAk94HVOTpMxdQLJDHsCECzA5tbMN08UOFHRYjFfQDihczhdkmGBQAAAABJRU5ErkJggg==',
			'E5C7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNEQxlCHUNDkMQCGkQaGB1AJKoYa4MAulgIK5hGuC80aurSpatWrcxCch9QvtG1gaGVAUUvWGwKqpgIUEwgAFWMtZXRIdAB1c2MIUA3o4gNVPhREWJxHwB9Mc0Etk5eUAAAAABJRU5ErkJggg==',
			'BB3B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QgNEQxhDGUMdkMQCpoi0sjY6OgQgi7WKNDo0BDqIoKljQKgDOyk0amrYqqkrQ7OQ3IemDrd5OOxAdws2Nw9U+FERYnEfAKvrzjMx8FGoAAAAAElFTkSuQmCC',
			'2943' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYQxgaHUIdkMREprC2MrQ6OgQgiQW0ijQ6THVoEEHWDRILdGgIQHbftKVLMzOzlmYhuy+AMdC1Ea4ODBkdGBpdQwNQzGNtYGl0aES1Q6QB6JZGVLeEhmK6eaDCj4oQi/sA6/TNkjqdrF0AAAAASUVORK5CYII=',
			'9D5C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQ1hDHaYGIImJTBFpZW1gCBBBEgtoFWl0bWB0YEEXm8rogOy+aVOnrUzNzMxCdh+rq0ijQ0OgA4rNrZhiAmA7AlHsALmF0dEBxS0gNzOEMqC4eaDCj4oQi/sAkcvLnmDmzO8AAAAASUVORK5CYII=',
			'32F9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7RAMYQ1hDA6Y6IIkFTGFtZW1gCAhAVtkq0ujawOgggiw2hQFZDOyklVGrli4NXRUVhuy+KQxTgOZNRdHbyhAAFGtAFWN0AIqh2AF0SwO6W0QDRENdgeYhu3mgwo+KEIv7ALxUyvp/tcVtAAAAAElFTkSuQmCC',
			'9E95' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WANEQxlCGUMDkMREpog0MDo6OiCrC2gVaWBtCMQm5uqA5L5pU6eGrcyMjIpCch+rq0gDQ0hAgwiyza0gHqqYAFCMEWgHshjELQ4ByO6DuJlhqsMgCD8qQizuAwAiUcqlnd9JBgAAAABJRU5ErkJggg==',
			'4718' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpI37poiGOkxhmOqALBbC0OgQwhAQgCTGCBRzDGF0EEESY53C0MowBa4O7KRp01aB4NQsJPcFANUgqQPD0FBGB4YpqOYxTGFtwBQD8tD0gsQYQx1Q3TxQ4Uc9iMV9AFfzy4/meGuMAAAAAElFTkSuQmCC',
			'55A3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkNEQxmmMIQ6IIkFNIg0MIQyOgSgiTE6OoBk4DAwQCSEFSgTgOS+sGlTly5dFbU0C9l9rQyNrgh1CLHQABTzAlpFwOqQxUSmsLayNgSiuIU1gBFkL4qbByr8qAixuA8ATUTOBUS0u+IAAAAASUVORK5CYII=',
			'8FDF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WANEQ11DGUNDkMREpog0sDY6OiCrC2gFijUEooiB1SHEwE5aGjU1bOmqyNAsJPehqcNpHk470NzCGgAUC2VEERuo8KMixOI+AJeRysqix4cKAAAAAElFTkSuQmCC',
			'45E2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nM3QIQ7AIAxA0SJ6g+4+TMwjqNlpiuAGwA0wnHIDVbLJLaF1LyT8FNpjBFbaf/rSxsg2W22eBAWcU2aGGUvKMJG/3wmpvlJyrdzaqfpcgnAIBP0H87A4t1C3NBvG3jKb8cg7+xXu992+9F14LMuy1mIxTwAAAABJRU5ErkJggg==',
			'F025' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGUMDkMQCGhhDGB0dHRhQxFhbWRsC0cREGh0aAl0dkNwXGjVtZdbKzKgoJPeB1bUyNIig652CLsbayhDA6CCC7hYHhgBU9zEEsIYGTHUYBOFHRYjFfQCcBcvgtP+9XgAAAABJRU5ErkJggg=='        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>