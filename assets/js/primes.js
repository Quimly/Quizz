'use strict';

require('../css/primes.css');

$(function ()
{

    // Créer un tableau de n nombres premiers

    function primes(n) {

        let prime = $([...Array(n).keys()]);

        prime.each(function (key, val)
        {
            for (let i = 2; i < val; i++)
            {
                let num = val / i;

                if (Number.isInteger(num))
                {
                    delete prime[key];
                    break;
                }
            }
        });
        return prime;
    }


    // Créer une table d'affichage

    (function()
    {
        let display = document.getElementById('life');
        let table = document.createElement('table');
        table.id = 'data';
        display.appendChild(table);

        for (let i = 100; i <= 400; i++)
        {
            let tr = document.createElement('tr');
            table.appendChild(tr);

            for (let j = 100; j <= 400; j++)
            {
                let td = document.createElement('td');
                td.id ='x' + j + 'y' + i;
                td.className = 'dead';

                td.onclick = function(){
                    if (this.classList.contains('alive')) this.className = 'dead';
                    else this.className = 'alive';
                };
                tr.appendChild(td);
            }
        }
    })();



    // Déterminer les coordonnées d'une spriale ayant pour origine le centre de la table

    function spiralPath()
    {
        let coords = $([...Array(150).keys()]);

        let x = 250;
        let y = 250;
        let n = 1;
        let count = 1;

        coords[0] = 'x250y250';

        coords.each(function()
        {
            x--;
            coords[count++] = 'x' + x + 'y' + y;

            for(let i = 1; i <= n; i++)
            {
                y--;
                coords[count++] = 'x' + x + 'y' + y;
            }

            for(let j = 1; j <= 3; j++)
            {
                for (let i = 1; i <= n + 1; i++)
                {
                    switch(j)
                    {
                        case 1: x++;
                            break;
                        case 2: y++;
                            break;
                        case 3: x--;
                            break;
                    }
                    coords[count++] = 'x' + x + 'y' + y;
                }
            }
            n += 2;
        });
        return coords;
    }

    let myCoords = spiralPath();
    let myPrimes = primes(90000);
    delete myPrimes[0];

    for(let val of myPrimes)
    {
        $('#' + myCoords[val]).css('background-color', 'lightblue');
    }
});