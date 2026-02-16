<?php
/* 
|--------------------------------------------------------------------------
| NFC Profile Page with Contact Icons
|--------------------------------------------------------------------------
*/
$homeLat = 14.666033514131605;
$homeLng = 121.04523130584789;
$homeLabel = "Jericho's Home";

// Replace these with your actual links
$facebookLink = "https://www.facebook.com/jerixhococs";
$instagramLink = "https://www.instagram.com/jericho_lym";
$emailAddress = "jerichomaghilom08@gmail.com";
$phoneNumber = "+639202515164";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NFC Profile – Jericho Maghilom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Include Font Awesome in your <head> if not already -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- Icons (Heroicons CDN) -->
    <script src="https://unpkg.com/feather-icons"></script>

    <style>
        .fade-in { animation: fadeIn 0.8s ease-out; }
        @keyframes fadeIn { from {opacity:0; transform: translateY(10px);} to {opacity:1; transform:translateY(0);} }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 min-h-screen text-white flex items-center justify-center p-4">

<div class="w-full max-w-md fade-in">

    <!-- Profile Card -->
    <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-6 border border-white/10">

        <div class="text-center">
            <h1 class="text-2xl font-bold">Jericho Maghilom</h1>
                    <!-- Contact Section -->
<div class="contact-info flex justify-center items-center">
  <p class="flex items-center gap-2">
    <i class="fas fa-phone-alt"></i> +63 912 345 6789
  </p>
</div>
            <p class="text-slate-300 text-sm">Philippines 🇵🇭</p>

            <p class="mt-3 text-slate-200">Thanks for tapping my NFC card 👋</p>
        </div>




    

        <!-- Visitor Info -->
        <div class="mt-6 space-y-2 text-sm">
            <div>
                <span class="text-slate-400">Visitor Coordinates:</span>
                <div id="coords" class="text-slate-200">Detecting location...</div>
            </div>
            <div>
                <span class="text-slate-400">Distance to My Home:</span>
                <div id="distance" class="text-slate-200">Calculating...</div>
            </div>
            <div id="error" class="text-red-400 hidden"></div>
        </div>

     

        <!-- Map -->
        <div id="map" class="mt-5 rounded-xl overflow-hidden hidden" style="height: 280px;"></div>

        <!-- Directions Button -->
        <button 
            id="directionsBtn"
            class="mt-5 w-full bg-indigo-500 hover:bg-indigo-600 transition rounded-xl py-2 font-medium hidden">
            Get Directions
        </button>

        <!-- Contact Icons -->
        <div class="mt-6 flex justify-center items-center gap-6 text-white text-xl">
            <a href="<?php echo $facebookLink; ?>" target="_blank" class="hover:text-blue-600 transition" title="Facebook">
                <i data-feather="facebook"></i>
            </a>
            <a href="<?php echo $instagramLink; ?>" target="_blank" class="hover:text-pink-500 transition" title="Instagram">
                <i data-feather="instagram"></i>
            </a>
            <a href="mailto:<?php echo $emailAddress; ?>" class="hover:text-green-400 transition" title="Email">
                <i data-feather="mail"></i>
            </a>
            <a href="tel:<?php echo $phoneNumber; ?>" class="hover:text-yellow-400 transition" title="Phone">
                <i data-feather="phone"></i>
            </a>
        </div>

    </div>

    <p class="text-center text-slate-500 text-xs mt-4">
        Location is only used within your browser
    </p>

</div>

<script>
feather.replace();

/* Config */
const HOME_LAT = <?php echo $homeLat; ?>;
const HOME_LNG = <?php echo $homeLng; ?>;
const HOME_LABEL = "<?php echo $homeLabel; ?>";

/* Haversine distance */
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a =
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat1*Math.PI/180) * Math.cos(lat2*Math.PI/180) *
        Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

function showError(message) {
    const errorEl = document.getElementById("error");
    errorEl.innerText = message;
    errorEl.classList.remove("hidden");
    document.getElementById("coords").innerText = "Unavailable";
    document.getElementById("distance").innerText = "Unavailable";
}

function initMap(visitorLat, visitorLng) {
    const mapEl = document.getElementById("map");
    mapEl.classList.remove("hidden");
    const map = L.map('map').setView([visitorLat, visitorLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    L.marker([visitorLat, visitorLng]).addTo(map).bindPopup("You are here 📍").openPopup();
    L.marker([HOME_LAT, HOME_LNG]).addTo(map).bindPopup(HOME_LABEL);

    const group = new L.featureGroup([
        L.marker([visitorLat, visitorLng]),
        L.marker([HOME_LAT, HOME_LNG])
    ]);
    map.fitBounds(group.getBounds(), { padding: [30,30] });
}

function enableDirections(visitorLat, visitorLng) {
    const btn = document.getElementById("directionsBtn");
    btn.classList.remove("hidden");
    btn.onclick = () => {
        const url = `https://www.google.com/maps/dir/${visitorLat},${visitorLng}/${HOME_LAT},${HOME_LNG}`;
        window.open(url, "_blank");
    };
}

if (!navigator.geolocation) {
    showError("Geolocation is not supported on this device.");
} else {
    navigator.geolocation.getCurrentPosition(
        (position) => {
            const visitorLat = position.coords.latitude;
            const visitorLng = position.coords.longitude;

            document.getElementById("coords").innerText =
                `${visitorLat.toFixed(5)}, ${visitorLng.toFixed(5)}`;

            const distance = calculateDistance(visitorLat, visitorLng, HOME_LAT, HOME_LNG);
            document.getElementById("distance").innerText = `${distance.toFixed(2)} km`;

            initMap(visitorLat, visitorLng);
            enableDirections(visitorLat, visitorLng);
        },
        (error) => {
            switch(error.code) {
                case error.PERMISSION_DENIED: showError("Location permission was denied."); break;
                case error.POSITION_UNAVAILABLE: showError("Location information is unavailable."); break;
                case error.TIMEOUT: showError("Location request timed out."); break;
                default: showError("An unknown error occurred."); break;
            }
        }
    );
}
</script>

</body>
</html>
