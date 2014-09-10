<?php

namespace Itkg\Mail;

use Zend\Mail\Message;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mail\Transport\Sendmail as SendmailTransport;

/**
 * Classe d'envoi d'un email
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 *
 * @package \Itkg\Mail
 */

class Client extends Message
{

    /**
     * Setter magic
     *
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * Getter magic
     *
     * @param string $name
     * @return string
     */
    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * Envoi un mail si les paramètre from, subject, bodytext et recipients sont renseignés
     *
     * @param string $from "From" address
     * @param string $subject message subject header value
     * @param mixed $bodytext String or Stream containing the content
     * @param string|Address\AddressInterface|array|AddressList|Traversable $recipients destinataires
     * @param string|Address|array|AddressList|Traversable $ccs destinataire(s) en copie
     * @param string|Address|array|AddressList|Traversable $bccs destinataire(s) en copie cachée
     * @param string[] $attachements tableau de filenames (tels que supportés par fopen)
     * @param array $output true si besoin d'afficher le message d'erreur (echo)
     * @param string|\Zend\Mail\Address\AddressInterface $sender nom de l'expediteur (ou adresse à afficher au destinataire)
     * @param string $bTransport true si l'on doit ajouter "-f[From email]" en parametre du constructeur
     * de Zend\Mail\Transport\Sendmail (cf ticket 233118 CPLUS TMA EC 2012,
     * ou ticket 235685 CPLUS EC 100% )
     *
     *
     * @return string|boolean en cas d'erreur, retourne une chaine de caractères
     * contenant le message d'erreur, sinon retourne true
     */
    public static function sendMessage ($from, $subject, $bodytext,
        $recipients = array(), $ccs = array(), $bccs = array(),
        $attachements = array(), $output = false, $sender = "",
        $bTransport = false,$encoding = "UTF-8"
    ) {
        $mail = new Message();

        if(!empty($from) && !empty($subject)) {
            $mail->addFrom(new \Zend\Mail\Address($from,iconv($encoding, "ASCII//TRANSLIT",$sender)))
                ->setSubject(iconv($encoding, "ASCII//TRANSLIT",$subject));

            if(!empty($recipients)){
                $mail->addTo($recipients);
            } else {
                $error = 'aucun destinataire renseigné.';
            }

            if(!empty($ccs)) {
                $mail->addCc($ccs);
            }

            if(!empty($bccs)) {
                $mail->addBcc($bccs);
            }

            $body = new MimeMessage();

            if(!empty($attachements)) {
                foreach ($attachements as $attachement) {
                    $join = new MimePart(fopen($attachement,'r'));
                    $join->type = mime_content_type($attachement);
                    $join->encoding = "base64";
                    $body->addPart($join);
                }
            }

            $html = new MimePart($bodytext);
            $html->type = "text/html";
            $body->addPart($html);

            $mail->setBody($body);

            $headers = $mail->getHeaders();
            $headers->setEncoding($encoding);
            $headers->removeHeader('Content-Type');
            $headers->addHeaderLine('Content-Type', 'text/html; charset='.$encoding);
            $mail->setHeaders($headers);

            if (!$bTransport) {
                $transport = new SendmailTransport();
            } else {
                $transport = new SendmailTransport("-f".$from);
            }

            if($mail->isValid()) {
                try {
                    $transport->send($mail);
                    //mise en commentaire du echo : le echo doit être 
                    //fait au niveau de l'appel à la méthode
                    //echo "Message sent!<br />\n";
                    return true;
                } catch (Exception $ex) {
                    $error = "Failed to send mail! " . $ex->getMessage() . "<br />\n";
                }
            } else {
                $error = 'Non valid mail structure.<br />\n';
            }
        } else {
            $error = 'Aucun expediteur ou sujet renseigné.<br />\n';
        }

        if(!empty($error)) {
            if($output) {
                echo $error;
            }
            return $error;
        } else {
            return true;
        }
    }
}