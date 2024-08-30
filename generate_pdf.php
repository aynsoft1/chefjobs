<?php
include_once("include_files.php");
require_once('tcpdf_library/config/tcpdf_config.php');
require_once('tcpdf_library/tcpdf.php');

$action = (isset($_GET['action']) ? $_GET['action'] : '');
$invoice_id = (isset($_GET['inv_id']) ? $_GET['inv_id'] : '');
$auth_type = (isset($_GET['q']) ? $_GET['q'] : '');

define("MESSAGE_ORDER_ERROR", "Something went wrong!...");

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // logo add
        $this->Image('img/'.DEFAULT_SITE_LOGO, 85, 0, 35, '', 'PNG', '', '', false, 300, '', false, false, 0, false, false, false);

        $custome_header = '
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        '.SITE_TITLE.'
                    </td>
                    <td></td>
                    <td>INVOICE</td>
                </tr>
            </tbody>
        </table>
        ';

        // Set font
        $this->SetFont('helvetica', 'B', 20);

        // $this->writeHTML($custome_header, true, false, true, false, 'C');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

    public function show_pdf($action, $invoice_id, $pdf, $currencies)
    {
        if ($action == 'invoice' AND tep_not_null($invoice_id)) {
            $data = new order($invoice_id);
            // set document information
            $pdf->SetCreator(SITE_TITLE);
            $pdf->SetAuthor(SITE_TITLE);
            $pdf->SetTitle(SITE_TITLE);
            $pdf->SetSubject(SITE_TITLE.' Invoice');
            // set default header data
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, SITE_TITLE, PDF_HEADER_STRING);
        
            // set header and footer fonts
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        
            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        
            // remove default header/footer
            // $pdf->setPrintHeader(false);
            // $pdf->setPrintFooter(false);
        
            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
            // set some language-dependent strings (optional)
            if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
                require_once(dirname(__FILE__).'/lang/eng.php');
                $pdf->setLanguageArray($l);
            }
        
            // add a page
            $pdf->AddPage();
            $billTo = '<td colspan="2">Bill To: <br>'.$data->customer['name'].'<br>'.$data->customer['city'].',&nbsp;'.$data->customer['postcode'].'<br>'.$data->customer['country'].',&nbsp;'.$data->customer['state'].'<br>'.$data->customer['email_address'].' <br></td>
                        <td colspan="2">Date:&nbsp;'.tep_date_short($data->info['date_purchased']).'<br>Invoice Number:&nbsp;'.$invoice_id.'</td>';
        
            $product_fee=tep_db_output($currencies->format($data->products['fee'], 
                                        ($data->products['currency']!=DEFAULT_CURRENCY?true:false), 
                                        DEFAULT_CURRENCY, ($data->products['currency']==DEFAULT_CURRENCY?$currencies->get_value($data->products['currency']):'')));
        
            $product_info='<tr><td>Product</td><td>'.$data->products['plan_type_name'].'</td></tr>
                                <tr><td>Total Price:</td><td>'.$product_fee.'</td></tr>';
        
            for ($i = 0, $n = sizeof($data->totals); $i < $n; $i++)
            {
                $product_info.='
                        <tr>
                        <td valign="top" class="label">'.$data->totals[$i]['title'].'</td>
                        <td valign="top" class="">'.$data->totals[$i]['text'].'</td>
                        </tr>'."\n";
            }
        
            // create some HTML content
            $html = '<div>
                        <table cellspacing="0" cellpadding="4" border="0" nobr="true">
                            <thead>
                                <tr>
                                    <th>'.SITE_TITLE.'</th>
                                    <th></th>
                                    <th>INVOICE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    '.$billTo.'
                                </tr>
                            </tbody>
                        </table>
                        <br>
                        <br>
                        <br>
        
                        <table cellspacing="0" cellpadding="4" border="0.5" nobr="true">
                            <tbody>
                                '.$product_info.'
                            </tbody>
                        </table>
        
                        <br>
                        <br>
                        <br>
                        <table>
                            <tbody>
                                <tr><td align="right">'.SITE_TITLE.'</td></tr>
                            </tbody>
                        </table>
                    </div>';
        
            // output the HTML content
            $pdf->writeHTML($html, true, false, true, false, '');
        
            // reset pointer to the last page
            $pdf->lastPage();
        
            //Close and output PDF document
            $pdf->Output('invoice.pdf', 'I');
        } else {
            print_r('Not found');
            exit;
        }
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

switch ($auth_type) {
    case 'recruiter':
        if(!$row_order_check=getAnyTableWhereData(ORDER_TABLE,"orders_id='".tep_db_input($invoice_id)."' and recruiter_id='".$_SESSION['sess_recruiterid']."'","orders_id,admin_comment"))
        {
        $messageStack->add_session(MESSAGE_ORDER_ERROR, 'error');
        tep_redirect(FILENAME_RECRUITER_ACCOUNT_HISTORY_INFO);
        } else {
            include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'order.php');
            $pdf->show_pdf($action, $invoice_id, $pdf, $currencies);
        }
        break;

    case 'jobseeker':
        if(!$row_order_check=getAnyTableWhereData(JOBSEEKER_ORDER_TABLE,"orders_id='".tep_db_input($invoice_id)."' and jobseeker_id='".$_SESSION['sess_jobseekerid']."'","orders_id,admin_comment"))
        {
        $messageStack->add_session(MESSAGE_ORDER_ERROR, 'error');
        tep_redirect(FILENAME_JOBSEEKER_ACCOUNT_HISTORY_INFO);
        } else {
            include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'jobseeker_order.php');
            $pdf->show_pdf($action, $invoice_id, $pdf, $currencies);
        }
        break;
    
    case 'admin':
        if (!check_login('admin')) {
            $_SESSION['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];
            $messageStack->add_session(MESSAGE_ORDER_ERROR, 'error');
            tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LOGIN));
        } else {
            include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'order.php');
            $pdf->show_pdf($action, $invoice_id, $pdf, $currencies);
        }
        break;
    
    default:
        print_r('Not found');
        exit;
        break;
}

?>