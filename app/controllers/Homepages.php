<?php
/**
 * Controller voor de Homepagina van Voedselbank Maaskantje
 * 
 * Volgt PSR-12 conventies, bevat uitgebreide commentaar, try-catch, logging, en is voorbereid op validatie en meldingen.
 * 
 * @author Zakaria
 */
class Homepages extends BaseController
{
    /**
     * Laadt de homepage met algemene informatie.
     * Bevat technische logging, foutafhandeling en terugkoppeling voor de gebruiker.
     *
     * @return void
     */
    public function index(): void
    {
        // Technische log: start van de homepage-aanroep
        error_log(date('[Y-m-d H:i:s]') . " Homepages::index aangeroepen");

        try {
            // Data voor de homepage, kan later uitgebreid worden met dynamische data
            $data = [
                'title' => 'Welkom bij Voedselbank Maaskantje',
                'subtitle' => 'Samen tegen voedselverspilling en armoede',
                'about' => 'Voedselbank Maaskantje zet zich in om voedseloverschotten te verdelen onder mensen die het hard nodig hebben. Met de hulp van vrijwilligers en donateurs zorgen wij ervoor dat niemand in onze regio zonder eten hoeft te zitten.',
                'mission' => 'Onze missie is om armoede te bestrijden en voedselverspilling tegen te gaan. We werken samen met lokale supermarkten, leveranciers en particulieren om voedsel te verzamelen en eerlijk te verdelen.',
                'contact' => 'Neem contact met ons op via info@voedselbankmaaskantje.nl of bel 06-12345678. Ons adres: Dorpsstraat 1, 1234 AB Maaskantje.'
            ];

            // Server-side validatie (voorbeeld, kan uitgebreid worden)
            foreach ($data as $key => $value) {
                if (empty($value)) {
                    // Log foutmelding
                    error_log(date('[Y-m-d H:i:s]') . " Waarde voor $key ontbreekt in Homepages::index");
                    // Toon melding aan gebruiker (kan later met flash-messages)
                    $data['melding'] = 'Er is een technische fout opgetreden. Probeer het later opnieuw.';
                }
            }

            // Laad de view met de data
            $this->view('homepages/index', $data);
        } catch (Exception $e) {
            // Log de exception
            error_log(date('[Y-m-d H:i:s]') . " Fout in Homepages::index: " . $e->getMessage());
            // Toon een nette foutmelding aan de gebruiker
            $data = [
                'title' => 'Fout',
                'subtitle' => '',
                'about' => '',
                'mission' => '',
                'contact' => '',
                'melding' => 'Er is een onverwachte fout opgetreden. Neem contact op met de beheerder.'
            ];
            $this->view('homepages/index', $data);
        }
    }
}