<?php require_once APPROOT . '/views/includes/header.php'; ?>
<link rel="stylesheet" href="/css/style.css">
<style>
    .vbn-main { min-height: 80vh; display: flex; align-items: center; justify-content: center; }
    .vbn-section { background: #fff; border-radius: 18px; box-shadow: 0 4px 24px rgba(238,123,0,0.08); padding: 2.5rem 2rem 2rem 2rem; margin-bottom: 2.5rem; }
    .vbn-title { color: #14193B; font-weight: 800; letter-spacing: -1px; font-size: 2.2rem; }
    .vbn-subtitle { color: #EE7B00; font-weight: 600; font-size: 1.3rem; margin-bottom: 1.2rem; }
    .vbn-btn { background: #EE7B00; color: #fff; border: none; border-radius: 8px; padding: 12px 28px; font-weight: 600; font-size: 1.1rem; transition: background 0.2s; margin: 0.5rem 0; display: inline-block; text-decoration: none; }
    .vbn-btn:hover { background: #d96c00; color: #fff; }
    .vbn-light { background: #DDD7D7; border-radius: 10px; padding: 1.2rem 1.5rem; margin-bottom: 1.2rem; font-weight: 600; color: #14193B; }
    .vbn-faq { background: #DDD7D7; border-radius: 12px; padding: 1.2rem 1.5rem; margin-bottom: 1.2rem; }
    .vbn-story { background: #fff; border-left: 6px solid #EE7B00; border-radius: 10px; padding: 1.2rem 1.5rem; margin-bottom: 1.2rem; box-shadow: 0 2px 8px rgba(20,25,59,0.06); }
    .vbn-story a { color: #EE7B00; font-weight: 600; text-decoration: underline; cursor: pointer; }
    .vbn-story a:hover { color: #14193B; text-decoration: none; }
    .vbn-footer { background: #14193B; color: #fff; padding: 2.5rem 0 1.5rem 0; margin-top: 4rem; }
    .vbn-footer a { color: #EE7B00; text-decoration: none; font-weight: 500; }
    .vbn-footer a:hover { text-decoration: underline; color: #fff; }
    .vbn-footer .footer-logo { max-width: 120px; margin-bottom: 1rem; }
    .vbn-social-icons img { height: 32px; margin: 0 8px; filter: grayscale(0.2) brightness(0.95); transition: filter 0.2s; }
    .vbn-social-icons img:hover { filter: none; }
    @media (max-width: 991px) { .vbn-title { font-size: 1.5rem; } .vbn-section { padding: 1.5rem 0.7rem; } }
</style>
<div class="container vbn-main">
    <div class="row w-100 justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="vbn-section text-center">
                <h1 class="vbn-title mb-2">Welkom bij Voedselbank Maaskantje</h1>
                <div class="vbn-subtitle">Samen tegen voedselverspilling en armoede</div>
                <p class="lead mb-3">Voedselbank Maaskantje zet zich in om voedseloverschotten te verdelen onder mensen die het hard nodig hebben. Met de hulp van vrijwilligers en donateurs zorgen wij ervoor dat niemand in onze regio zonder eten hoeft te zitten.</p>
                <p class="mb-3">Onze missie is om armoede te bestrijden en voedselverspilling tegen te gaan. We werken samen met lokale supermarkten, leveranciers en particulieren om voedsel te verzamelen en eerlijk te verdelen.</p>
                <a href="/overzicht-voedselpakket" class="vbn-btn">Naar overzicht voedselpakket</a>
            </div>
            <div class="vbn-section text-center">
                <h2 class="vbn-title mb-3">Ik zoek hulp</h2>
                <div class="vbn-light mb-2">Voedselhulp voor als je het een tijdje echt niet redt</div>
                <p>Wij zijn er voor vrijwel iedereen* die voedselhulp nodig heeft. Je krijgt sowieso vier weken boodschappen. We bespreken samen of je na die vier weken klant kunt blijven.</p>
                <p style="font-size:0.95rem;color:#888;">*Helaas, we kunnen geen hulp bieden aan uitwonende studenten, ongedocumenteerde personen, bewoners van een AZC en daklozen</p>
                <a href="#" class="vbn-btn mb-2">Meld je aan</a>
            </div>
            <div class="vbn-section text-center">
                <h2 class="vbn-title mb-3">Zo werkt het bij de voedselbank</h2>
                <p>Minder of geen werk, ziekte of hoge vaste lasten. En dan is er ineens te weinig geld. Dat kan iedereen overkomen. Daarom zijn we er. Elke week helpen we tienduizenden mensen in Nederland. Met gratis, goed voedsel voor 2 tot 3 dagen. Ook voor jou. Je bent welkom.</p>
                <div class="row mb-3">
                    <div class="col-6 col-md-3 mb-2"><div class="vbn-light">Alleenstaand<br><b>&lt; €325</b></div></div>
                    <div class="col-6 col-md-3 mb-2"><div class="vbn-light">Samenwonend<br><b>&lt; €445</b></div></div>
                    <div class="col-6 col-md-3 mb-2"><div class="vbn-light">Alleenstaande met kind<br><b>&lt; €445</b></div></div>
                    <div class="col-6 col-md-3 mb-2"><div class="vbn-light">Gezin (2+2)<br><b>&lt; €685</b></div></div>
                </div>
                <a href="#" class="vbn-btn">Meld je aan voor gratis voedselhulp</a>
            </div>
            <div class="vbn-section text-center">
                <h2 class="vbn-title mb-3">Veelgestelde vragen</h2>
                <div class="vbn-faq"><b>Telt mijn spaargeld mee in de berekening?</b><br> Nee, spaargeld telt niet mee.</div>
                <div class="vbn-faq"><b>Hoeveel maaltijden kan een gezin van 1 week voedselondersteuning maken?</b><br> Genoeg voor 2-3 dagen per week.</div>
                <div class="vbn-faq"><b>Telt de overwaarde van mijn huis mee in de berekening?</b><br> Nee, alleen je maandelijkse lasten en inkomsten tellen mee.</div>
                <div class="vbn-faq"><b>Zijn de regels voor een voedselondersteuning overal hetzelfde?</b><br> De basisregels zijn gelijk, maar er kunnen lokale verschillen zijn.</div>
                <div class="vbn-faq"><b>Helpen de voedselbanken ook kinderen?</b><br> Ja, ook kinderen krijgen voedselondersteuning.</div>
                <div class="vbn-faq"><b>Hoe lang krijg je voedselondersteuning?</b><br> Minimaal 4 weken, daarna wordt samen gekeken naar verlenging.</div>
                <div class="vbn-faq"><b>Wat als je net niet in aanmerking komt?</b><br> We kijken altijd samen naar je situatie.</div>
                <div class="vbn-faq"><b>Wat gebeurt er met de informatie die ik deel met de voedselbank?</b><br> Je gegevens worden vertrouwelijk behandeld.</div>
                <div class="vbn-faq"><b>Wat zijn vaste lasten?</b><br> Huur, energie, zorgverzekering, enz.</div>
            </div>
            <div class="vbn-section text-center">
                <h2 class="vbn-title mb-3">Verhalen</h2>
                <div class="vbn-story"><b>Els en Bente</b> hadden last van voedselbankschaamte: ‘Mijn wereld stortte in’<br><a data-bs-toggle="modal" data-bs-target="#storyModal1">Lees meer</a></div>
                <div class="vbn-story"><b>Melanie (52)</b> – Chronische ziekte en hoge zorgkosten<br><a data-bs-toggle="modal" data-bs-target="#storyModal2">Lees meer</a></div>
                <div class="vbn-story"><b>Alex (38)</b> – Toen ik kwam zag ik dat iedereen een ander verhaal had<br><a data-bs-toggle="modal" data-bs-target="#storyModal3">Lees meer</a></div>
            </div>
        </div>
    </div>
</div>
<!-- Modals voor verhalen -->
<div class="modal fade" id="storyModal1" tabindex="-1" aria-labelledby="storyModal1Label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="storyModal1Label">Els en Bente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Els en Bente hadden last van voedselbankschaamte: ‘Mijn wereld stortte in’.<br>Toen ze voor het eerst bij de voedselbank kwamen, voelden ze zich onzeker. Maar ze ontdekten dat iedereen een eigen verhaal heeft en dat hulp vragen juist krachtig is. Nu helpen ze zelf als vrijwilliger.</p>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="storyModal2" tabindex="-1" aria-labelledby="storyModal2Label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="storyModal2Label">Melanie (52)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Melanie heeft een chronische ziekte en hoge zorgkosten. Dankzij de voedselbank hoeft ze zich minder zorgen te maken over boodschappen en kan ze zich focussen op haar gezondheid.</p>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="storyModal3" tabindex="-1" aria-labelledby="storyModal3Label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="storyModal3Label">Alex (38)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Alex kwam bij de voedselbank en zag dat iedereen een ander verhaal had. Hij voelde zich direct welkom en kreeg niet alleen voedsel, maar ook nieuwe hoop.</p>
      </div>
    </div>
  </div>
</div>
<footer class="vbn-footer">
    <div class="container">
        <div class="row align-items-center mb-4">
            <div class="col-12 text-center mb-3">
                <div class="vbn-social-icons">
                    <a href="#"><img src="/img/facebook.png" alt="Facebook"></a>
                    <a href="#"><img src="/img/instagram.png" alt="Instagram"></a>
                    <a href="#"><img src="/img/twitter.png" alt="Twitter"></a>
                </div>
            </div>
            <div class="col-12 text-center">
                <div class="d-flex flex-wrap justify-content-center gap-3 mb-2">
                    <a href="#">Ik zoek hulp</a>
                    <a href="#">Locaties</a>
                    <a href="#">Kom ik in aanmerking?</a>
                    <a href="#">Help mee</a>
                    <a href="#">Gelddonatie</a>
                    <a href="#">Voedseldonatie</a>
                    <a href="#">Word vrijwilliger</a>
                    <a href="#">Start een actie</a>
                </div>
                <div class="mb-2">
                    <a href="#">Over ons</a> |
                    <a href="#">Organisatie</a> |
                    <a href="#">Bestuur</a> |
                    <a href="#">Vacatures</a> |
                    <a href="#">Nieuws</a> |
                    <a href="#">Voedingsbodem</a> |
                    <a href="#">Account V-bodem</a> |
                    <a href="#">Voedselveiligheid</a> |
                    <a href="#">Contact</a> |
                    <a href="#">Voor de pers</a> |
                    <a href="#">Klachtenregeling</a> |
                    <a href="#">Vertrouwenspersonen</a>
                </div>
                <div>© 2025 Voedselbank Maaskantje &nbsp; | &nbsp; Privacyverklaring</div>
            </div>
        </div>
    </div>
</footer>
<?php require_once APPROOT . '/views/includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>