<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.used-design.com
 * @since      0.0.1
 *
 * @package    UsedDesign
 * @subpackage UsedDesign/admin/partials
 */


function used_design_plugin_options_page() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }


    // variables for the field and option names 
    $opt_name = 'useddesign_api_token';
    $hidden_field_name = 'useddesign_options_submit_hidden';
    $data_field_name = 'useddesign_api_token';

    // Read in existing option value from database
    $opt_val = get_option( $opt_name );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val = $_POST[ $data_field_name ];

        // Save the posted value in the database
        update_option( $opt_name, $opt_val );


        // Put a "settings saved" message on the screen
        ?>
        <div class="updated"><p><strong>Ihre Einstellungen wurden gespeichert</strong></p></div>
        <?php

    }
    ?>
    
    <!-- // SETTINGS EDITING SCREEN -->
    <div class="wrap">
        <h1>used-design Plugin Options</h1>
        <p>Nutzen Sie diese Seite um die nötigen Einstellungen vorzunehmen.</p>

        <!-- // SETTINGS FORM -->
        <form name="form1" method="post" action="">
            <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

            <p>used-design API Token: 
                <input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="60">
            </p>
            <small>Ihren persönlichen API Token finden Sie auf der used-design Webseite unter <a href="https://www.used-design.com/user/links" target="_blank">Webseite verlinken</a></small>

            <p class="submit">
                <input type="submit" name="Submit" class="button-primary" value="Speichern" />
            </p>

        </form>
        <hr />

        <!-- // DESCRIPTION - TUTORIAL -->
        <h2>Anleitung</h2>
        <h4>Alle Angebote anzeigen</h4>
        <p>
            Kopieren Sie den folgenden Shortcode an die gewünschte Stelle Ihrer Angebotsseite <br>
            <pre>[used_design_offers_grid]</pre>
        </p>
        
        <br>
        <h4>Angebote filtern</h4>
        <p>
            Zusätzlich können Sie Ihre Angebote filtern. <br>
            Fügen Sie dazu dem Shortcode die entsprechenden Parameter hinzu: <br>
            Hauptkategorie = cat-main <br>
            Unterkategorie = cat-sub <br>
            Hersteller = manufacturer <br>
            Freitextsuche = s <br>
            Max Anzahl gefundene Angebote = show <br>
            <pre>[used_design_offers_grid cat-main="1" manufacturer="31"]</pre>
            <pre>[used_design_offers_grid cat-main="4" s="Lounge Chair" show="9"]</pre>
        </p>

        <br>
        <h4>So finden Sie die ID's für die Hauptkategorie und Hersteller</h4>
        <ol>
            <li>Öffnen Sie die used-design <a href="https://www.used-design.com/angebote/suchen?cat-main=1&manufacturer=31" target="_blank">Suchseite</a></li>
            <li>Wählen Sie die gewünschte Hauptkategorie und Hersteller aus</li>
            <li>Führen Sie die Suche aus</li>
            <li>Auf der Ergebnisseite können Sie jetzt aus der URL-Zeile die benötigten ID's auslesen</li>
        </ol>
        <br>
        <img src="https://s3.eu-central-1.amazonaws.com/ud-app/app-public-img/ud-wp-plugin-tutorial-1.jpg" alt="" width="800" height="auto">
        <hr />

        <br>
        <h2>Erweiterte Einstellungen</h2>
        <h4>Anzahl Spalten</h4>
        <p>Mit dem Parameter "col-max" können Sie die maximale Anzahl der Spalten vordefinieren. <br>
        Ohne diese Angabe werden auf Großen Displays 4 Spalten gezeigt. Sie haben die Wahl zwischen 1-4 Spalten <br>
        <pre>[used_design_offers_grid col-max="3"]</pre></p>
    </div>

    <?php
}