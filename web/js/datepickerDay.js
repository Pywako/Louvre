$(function () {
    var now     = new Date();
    var year    = now.getFullYear();
    var month   = now.getMonth();
    var day     = now.getDate();
    var hours   = now.getHours();
    var minutes = now.getSeconds();

    function easterForYear (year) {
        var a = year % 19;
        var b = Math.floor(year / 100);
        var c = year % 100;
        var d = Math.floor(b / 4);
        var e = b % 4;
        var f = Math.floor((b + 8) / 25);
        var g = Math.floor((b - f + 1) / 3);
        var h = (19 * a + b - d - g + 15) % 30;
        var i = Math.floor(c / 4);
        var k = c % 4;
        var l = (32 + 2 * e + 2 * i - h - k) % 7;
        var m = Math.floor((a + 11 * h + 22 * l) / 451);
        var n0 = (h + l + 7 * m + 114)
        var n = Math.floor(n0 / 31) - 1;
        var p = n0 % 31 + 1;
        var finalDate = new Date(year,n,p);
        return finalDate;
    }

    var easter = easterForYear(year);
    var easterMonth =easter.getMonth();
    var easterDay =easter.getDay();

    var paques = [year];
    var ascension = [];
    var pentecote = [];


    $('.datepicker').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 2, // Creates a dropdown of 15 years to control year
        labelMonthNext: 'Mois suivant',
        labelMonthPrev: 'Mois précédent',
        labelMonthSelect: 'Selectionner le mois',
        labelYearSelect: 'Selectionner une année',
        monthsFull: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
        monthsShort: ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec'],
        weekdaysFull: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
        weekdaysShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
        weekdaysLetter: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
        today: 'Aujourd\'hui',
        clear: 'Réinitialiser',
        close: 'Fermer',
        format: 'dd/mm/yyyy',
        firstDay: 'Mon',
        min: new Date(),
        max: [year + 1, month, day],
        disable: [
            2, 7,
            [year, 0, 1],
            //Lundi paque
            [year, 4, 1],
            [year, 4, 8],
            // Jeudi ascension
            // Lundi pentecôte
            [year, 6, 14],
            [year, 7, 15],
            [year, 10, 1],
            [year, 10, 11],
            [year, 11, 25],
        ]
    });
});