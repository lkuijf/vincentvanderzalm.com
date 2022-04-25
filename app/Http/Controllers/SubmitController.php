<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubmitController extends Controller
{
    public function submitContactForm(Request $request) {
        // $validated = $request->validate([
        //     'Naam' => 'required',
        //     'E-mail_adres' => 'required|email',
        //     'Bericht' => 'required',
        // ],[
        //     'Naam.required'=> 'Geef a.u.b. een naam op.',
        //     'E-mail_adres.required'=> 'Geef a.u.b. een e-mail adres op.',
        //     'E-mail_adres.email'=> 'Het e-mail adres is niet juist geformuleerd.',
        //     'Bericht.required'=> 'Er is geen bericht ingevoerd.',
        // ]);
        $toValidate = array(
            'Naam' => 'required',
            'E-mail_adres' => 'required|email',
            'Bericht' => 'required',
        );
        $validationMessages = array(
            'Naam.required'=> 'Geef a.u.b. een naam op.',
            'E-mail_adres.required'=> 'Geef a.u.b. een e-mail adres op.',
            'E-mail_adres.email'=> 'Het e-mail adres is niet juist geformuleerd.',
            // 'Bericht.required'=> 'Er is geen bericht ingevoerd.',
        );
        /*  Using manually created validator, this line:
            $validated = $request->validate($toValidate,$validationMessages);
            is not redirecting properly when requesting over HTTPS
        */
        // $validated = $request->validate($toValidate,$validationMessages);
        $validator = Validator::make($request->all(), $toValidate, $validationMessages);
        if($validator->fails()) {
            return redirect('/contact')
                        ->withErrors($validator)
                        ->withInput();
            // return redirect()->back()->withErrors($validator)->withInput();
        }


        // $to_email = 'leon.kuijf@gmail.com';
        $to_email = 'info@vincentvanderzalm.com';
        // $to_email = 'frans@tamatta.org, rense@tamatta.org';
        $subjectCompany = 'Ingevuld contactformulier vanaf Vincentvanderzalm.com';
        $subjectVisitor = 'Kopie van uw bericht aan Vincentvanderzalm.com';

        $messages = $this->getHtmlEmails($request->all(), 'https://vincentvanderzalm.com/statics/email/vincent-van-der-zalm.png', 'De volgende gegevens zijn achtergelaten door de bezoeker.', 'Bedankt voor uw reactie. De volgende informatie hebben wij ontvangen:');

        $headers = array(
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=ISO-8859-1",
            "From: Vincent van der Zalm <contactformulier@vincentvanderzalm.com>",
            "Reply-To: info@vincentvanderzalm.com",
            "Bcc: leon@wtmedia-events.nl",
            // "X-Priority: 1",
        );
        $headers = implode("\r\n", $headers);
        mail($to_email, $subjectCompany, $messages[0], $headers);
        mail($request->get('E-mail_adres'), $subjectVisitor, $messages[1], $headers);
        // mail($to_email, $subject, $message);
        // return back()->with('success', 'Bedankt dat u contact met ons heeft opgenomen, we zullen uw bericht zo snel mogelijk in behandeling nemen!');
        return redirect('/contact')->with('success', 'Bedankt dat u contact met ons heeft opgenomen, we zullen uw bericht zo snel mogelijk in behandeling nemen!');

        // $to_email = 'leon.kuijf@gmail.com';
        // // $to_email = 'frans@tamatta.org, rense@tamatta.org';
        // $subjectCompany = 'Sample aanvraag vanaf Vincentvanderzalm.com';
        // $subjectVisitor = 'Kopie van uw sample aanvraag aan Vincentvanderzalm.com';
        // // $message = 'De volgende informatie is verzonden:
        
        // //     Naam: ' . $request->get('Naam') . '
        // //     Email adres: ' . $request->get('E-mail_adres') . '
        // //     Bericht: ' . $request->get('Bericht') . '
        // //     ';
        // $messages = $this->getHtmlEmails($request->all(), 'https://jusbros.nl/statics/email/jusbros.png', 'De volgende gegevens zijn achtergelaten door de bezoeker.', 'Bedankt voor uw sample aanvraag. De volgende informatie hebben wij ontvangen:');

        // $headers = array(
        //     "From: contactformulier@vincentvanderzalm.com",
        //     "MIME-Version: 1.0",
        //     "Content-Type: text/html; charset=ISO-8859-1",
        //     "X-Priority: 1",
        // );
        // $headers = implode("\r\n", $headers);
        // // mail($to_email, $subject, $message, $headers);
        // mail($to_email, $subject, $message);
        // return back()->with('success', 'Bedankt dat u contact met ons heeft opgenomen, we zullen uw bericht zo snel mogelijk in behandeling nemen!');
    }
    public function submitBestellenForm(Request $request) {
        $validated = $request->validate([
            'Betreft' => 'required',
            'Bedrijfsnaam' => 'required',
            'Contactpersoon' => 'required',
            'Emailadres' => 'required|email',
            'Bericht' => 'required',
        ],[
            'Betreft.required'=> 'Geef a.u.b. de reden van toenadering aan.',
            'Bedrijfsnaam.required'=> 'Geef a.u.b. een bedrijfsnaam op.',
            'Contactpersoon.required'=> 'Geef a.u.b. een contactpersoon op.',
            'Emailadres.required'=> 'Geef a.u.b. een e-mail adres op.',
            'Emailadres.email'=> 'Het e-mail adres is niet juist geformuleerd.',
            'Bericht.required'=> 'Er is geen bericht ingevoerd.',
        ]);

        $to_email = 'leon.kuijf@gmail.com';
        // $to_email = 'frans@tamatta.org, rense@tamatta.org';
        $subject = 'Ingevuld bestelformulier vanaf ......nl';
        $message = 'De volgende informatie is verzonden:
        
            Betreft: ' . $request->get('Betreft') . '
            Bedrijfsnaam: ' . $request->get('Bedrijfsnaam') . '
            Contactpersoon: ' . $request->get('Contactpersoon') . '
            Email adres: ' . $request->get('Emailadres') . '
            Bericht: ' . $request->get('Bericht') . '
            ';

        $headers = array(
            "From: bestelformulier@.....nl",
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=ISO-8859-1",
            "X-Priority: 1",
        );
        $headers = implode("\r\n", $headers);
        // mail($to_email, $subject, $message, $headers);
        mail($to_email, $subject, $message);
        return back()->with('success', 'Bedankt dat u contact met ons heeft opgenomen, we zullen uw bericht zo snel mogelijk in behandeling nemen!');
    }
    public function getHtmlEmails($values, $imgLocation, $introTextCompany, $introTextVisitor) {
        $message1 = '';
        $message2 = '';
        $topHtml = '
        <html><body>
        <!--[if mso]>
        <table cellpadding="0" cellspacing="0" border="0" style="padding:0px;margin:0px;width:100%;">
            <tr>
                <td style="padding:0px;margin:0px;">&nbsp;</td>
                <td style="padding:0px;margin:0px;" width="500">
        <![endif]-->
                    <div style="
                        max-width: 500px;
                        padding: 20px;
                        font-family: verdana, arial;
                        font-size: 14px;
                        margin-left: auto;
                        margin-right: auto;
                        background-color: #FFF;
                        border: 1px solid #CCC;
                    ">
                    <p style="text-align:center;"><img src="' . $imgLocation . '" alt="logo" /></p>
        ';

        $bottomHtml = '';
        foreach($values as $i => $v) {
            if($i == '_token' || $i == 'g-recaptcha-response') continue;
            $bottomHtml .= '
            <p>
                ' . str_replace('_', ' ', $i) . ':<br />
                <strong>' . (trim($v) == ''?'-':$v) . '</strong>
            </p>
            ';
        }
        $bottomHtml .= '
                    </div>
        <!--[if mso]>
                </td>
                <td style="padding:0px;margin:0px;">&nbsp;</td>
            </tr>
        </table>
        <![endif]-->
        </body></html>
        ';

        $message1 = $topHtml . '<p>' . $introTextCompany . '</p>' . $bottomHtml;
        $message2 = $topHtml . '<p>' . $introTextVisitor . '</p>' . $bottomHtml;

        return array($message1, $message2);
    }
}