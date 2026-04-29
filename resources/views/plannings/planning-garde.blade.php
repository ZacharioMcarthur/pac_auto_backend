<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Planning Garde</title>
</head>

<body>
    <style>
        /* CLIENT-SPECIFIC STYLES */
        #outlook a {
            padding: 0;
        }

        /* Force Outlook to provide a "view in browser" message */
        .ReadMsgBody {
            width: 100%;
        }

        .ExternalClass {
            width: 100%;
        }

        /* Force Hotmail to display emails at full width */
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
            line-height: 100%;
        }

        /* Force Hotmail to display normal line spacing */
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        /* Prevent WebKit and Windows mobile changing default text sizes */
        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        /* Remove spacing between tables in Outlook 2007 and up */
        img {
            -ms-interpolation-mode: bicubic;
        }

        /* Allow smoother rendering of resized image in Internet Explorer */

        /* RESET STYLES */
        body {
            margin: 0;
            padding: 0;
            font-size: 13px;
            font-family: Arial;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse !important;
        }

        body {
            height: 100% !important;
            margin: 0;
            padding: 0;
            width: 100% !important;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse !important;
        }

        body {
            height: 100% !important;
            margin: 0;
            padding: 0;
            width: 100% !important;
        }
    </style>
    <div style="width: 100%;">
        <div style="display: inline-block;width: 100%; background: #4974a5; ">
            <img width="20%" src="./assets/parc-logo.png" alt="PAC-logo">
        </div>
    </div>   <br>
    <div style="text-align: center; width: 98%; background: #F2F3F4; padding: 7px; margin-top: 5px;">
        <b>PLANNING DE GARDE CHAUFFEURS N<sup>o</sup> : {{ $planning_garde_id }}</b>
    </div> <br>
    <div style="text-align: right; width: 98%; padding: 7px; margin-top: 15px; color:#d9534f;">
        <b>PERIODE DU {{ $planning_garde->date_debut }} {{ $planning_garde->heure_debut }} AU {{ $planning_garde->date_fin }} {{ $planning_garde->heure_fin }}</b>
    </div>
    <div style="margin-top: 1px; width: 100%;">
        <table style="width: 100%;">
            <thead>
                <tr style="background: #F2F3F4">
                    <th style="padding: 2px; width: 5% !important">N°</th>
                    <th style="padding: 2px; width: 5% !important">MATRICULE</th>
                    <th width="30px;" style="padding: 4px;width: 20%">NOM</th>
                    <th style="padding: 2px;width: 25%">PRENOMS</th>
                    <th style="padding: 2px;width: 20%">EMAIL</th>
                    <th style="padding: 2px;width: 25%">TELEPHONE</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1 ?>
                @foreach ($plannings as $key => $planning)
                <tr style="border-bottom: solid 1px #D0D0D0; padding: 2px; padding-top: 3px;">
                    <td style="border-bottom: solid 1px #D0D0D0;padding: 2px;text-align: center;">
                        {{ $i }}
                    </td>
                    <td style="border-bottom: solid 1px #D0D0D0;padding: 2px;text-align: center;">
                        {{ $planning->chauffeur->matricule }}
                    </td>
                    <td style="border-bottom: solid 1px #D0D0D0;padding: 2px ;text-align: center;">
                        {{ $planning->chauffeur->user->nom }}
                    </td>
                    <td style="border-bottom: solid 1px #D0D0D0;padding: 2px;text-align: right; text-align: center;">
                        {{ $planning->chauffeur->user->prenom }}
                    </td>
                    <td style="border-bottom: solid 1px #D0D0D0;padding: 2px;text-align: right; padding-right:3px; text-align: center;">
                        {{ $planning->chauffeur->email }}
                    </td>
                    <td style="border-bottom: solid 1px #D0D0D0;padding: 2px;text-align: right; padding-right:3px; ;text-align: center;">
                        {{ $planning->chauffeur->contact }}
                    </td>
                </tr>
                <?php $i++ ?>  
                @endforeach      
            </tbody>
        </table>
    </div>
    <div style="width: 100%; margin-top: 200px; height: 110px !important; ">
        <div style="width: 60%; padding: 10px; display:inline-block;">
           <b>Cotonou, le {{date('d/m/Y')}}</b> 
        </div>
        <div style="width: 30%; display:inline-block; text-align: right;">
            <p style="text-align: right;">
                Le Chef du Service des Moyens Généraux<br /><br /><br /><br />
                <b><u>Isaac AFOUDA</u></b> 
            </p>
        </div>
    </div>
    <div class="footer" style="width: 100%; position: absolute; bottom: 0; background-color:#4974a5; ">
        <div style="bbackground-color:#4974a5; border-radius: 0px 0px 0px 0px; margin-top: 10px !important; text-align: center;">
            <div style="text-align: center; display: inline-block; color: white; margin-bottom: 3px; font-size: 14px; margin-left: 3%;">
                <b>BP 927 , Boulevard de la Marina, Cotonou - Bénin</b><br>
                <b>+229 21 31 52 80, contact@pac.bj</b><br>
                <b>www.portcotonou.com</b>
            </div>
        </div>
    </div>

</body>

</html>