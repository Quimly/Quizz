require('../css/game_life.css');

$(function(){
	$('#stop').hide();
	//__ Initialisation du tour actuel
	$tour = 0;
	
	//__ Modifie la taille des cases en fonctionde la variable cellSier définit en php passé en javascript dans index.php
	$('#gameTable td').css({"width": cellSize + "px", "height": cellSize + "px"});
	
	//__Variable qui va representer la grille de traitement
	let table = new Object();
	
	//__ On définit le nombre de ligne en fonction de nbLine et le nombre de colonne en fonction de nbCol, elles sont definit en php passé en javascript dans index.php
	for ( var i = 1; i <= nbLine; i++) {
		
		//__ Pour chaque ligne on crée un nouvel objet
		table[i] = new Object();
		
		for ( var j = 1; j <= nbCol; j++) {
			
			//__ Pour chaque case on crée un nouvel objet
			table[i][j] = new Object();
			
			//__ Chaque case à deux parametres : state et id
			
			//__On définit 2 états dans state, pour permettre de définir actuel de l'état du tour suivant
			table[i][j]['state'] = new Object();
			table[i][j]['state'][getState()] = 0 ;
			table[i][j]['state'][1 - getState()] = 0 ;
			
			//__L'id qui permet de retrouver sur la grille d'affichage la case que l'on traite
			table[i][j]['id'] = 'X' + j + 'Y' + i;
		}
	}	
	
	//__ Permet de modifier la taille des cellules dynamiquement si l'utilisateur bouge le curseur
	$('#cellSize').on('change', function(){
		let size = $(this).val();
		$('#gameTable td').css({"width": size + "px", "height": size + "px"});
	});
	
	//__ Permet de modifier la vitesse du jeu dynamiquement si l'utilisateur bouge le curseur
	$('#speed').on('change', function(){

		speed = $(this).val();
		
		$('#stop').trigger('click');

	});
	
	//__ Retourne l'etat actuel (à partir du nombre de tour pair ou impair)
	function getState() {
		return $tour % 2 == 0 ? 0 : 1;
	}
	
	//__ A partir de l'id de la grille d'affichage, retourne un objet qui contient les coordonnées de la case
	function getCoordonates(id){
		
		let pattern = /X([0-9]+)Y([0-9]+)/;
		let matches = id.match(pattern);
		
		return {'X': parseInt(matches[1]), 'Y': parseInt(matches[2]) };
		
	}
		
	//__ Inverse l'etat d'une cellule (morte ou vivante)
	function toggleStatus(coordinates){

		table[coordinates['Y']][coordinates['X']]['state'][getState()] = 1 - table[coordinates['Y']][coordinates['X']]['state'][getState()];

		if(table[coordinates['Y']][coordinates['X']]['state'][getState()] == 1) {
		
			$('#' + table[coordinates['Y']][coordinates['X']]['id']).addClass('alive');
			
		} else {
			
			$('#' + table[coordinates['Y']][coordinates['X']]['id']).removeClass('alive');
		}
		
	}
	
	//__ Retourne les cellules voisines d'une cellule
	function getNeighbours(coordinates) {
		
		//__Contient tout les voisin théorique( soit 8 max)
		let theorical_neighbours = [
				{'X': coordinates['X']-1, 'Y': coordinates['Y']-1},
				{'X': coordinates['X'], 'Y': coordinates['Y']-1},
				{'X': coordinates['X']+1, 'Y': coordinates['Y']-1},
				{'X': coordinates['X']-1, 'Y': coordinates['Y']},
				{'X': coordinates['X']+1, 'Y': coordinates['Y']},
				{'X': coordinates['X']-1, 'Y': coordinates['Y']+1},
				{'X': coordinates['X'], 'Y': coordinates['Y']+1},
				{'X': coordinates['X']+1, 'Y': coordinates['Y']+1}
		];
		
		let actual_neighbours = [];
		
		//__ Pour chaque voisin théorique, on vérifie qu'il existe dans la grille, par exemple si la cellule et en bordure elle n'aura pas  8 voisin donc il faut le vérifier
		for ( var neighbour of theorical_neighbours) {
			
			if(isExist(neighbour)) {			
				 actual_neighbours.push(table[neighbour['Y']][neighbour['X']]);
				
			}
		}
		
		return actual_neighbours;
		
	}
	
	//__ On vérifie l' existance d'un voisin, grâce à son id
	function isExist(neighbour) {

		if($('#' + 'X' + neighbour['X'] + 'Y' + neighbour['Y']).length == 0) {
			
			return false;
		}
		
		return true;
	}
	
	//__ Détermine l'état d'une cellule pour le tour suivant (morte ou vivante)
	function getNewState(cell){
		
		var neighboursAlive = 0;
		
		var currentState = cell['state'][getState()];
		
		var neigbours = getNeighbours(getCoordonates(cell['id']));
		
		for( var neighbour of neigbours) {
			
			if(neighbour['state'][getState()] == 1) {
				
				neighboursAlive++
			}
		}
		

		if(currentState == 0) {
			if(neighboursAlive == 3) {
				return 1;
			} else {
				return 0;
			}
			
		}
		if(currentState == 1) {
			if(neighboursAlive == 2 || neighboursAlive == 3) {
				return 1;
			} else {
				return 0;
			}
		}			
	}
	
	//__ Permet de créer un pattern de départ de cellules vivantes
	$('#gameTable').on('click', $('#gameTable td'), function(e){
		
		let coordinates = getCoordonates($(e.target).attr('id'));
		
		toggleStatus(coordinates);					
		
	});
	
	//__ Détermine l'etat du jeu au tour suivant (toutes les cellules morte et vivantes)
	function calculateNextStep(){

		for ( var line in table) {

			for( var cell in table[line]) {
				
				table[line][cell]['state'][1-getState()] = getNewState(table[line][cell]) == 1 ? 1 : 0;
				
			}
		}
	};
	
	//__ Met à jour l'affichage
	function refreshGrid(){
		
		for ( var line in table) {

			for( var cell in table[line]) {
				
				if(table[line][cell]['state'][1-getState()] == 1) {
					
					$('#' + table[line][cell]['id']).addClass('alive');
					
				} else {
					
					$('#' + table[line][cell]['id']).removeClass('alive');
				}
				
			}
		}
		
	}
	
	//__ Determine le vitesse du jeu
	var loop;
	
	//__ lance le jeu
	$('#start').on('click', function(e){
			
		loop = setInterval(function(){
				
					calculateNextStep();
							
					refreshGrid();
					
					$tour++;
					
					$('#start').hide();
					$('#stop').show()
						
				}, speed);
	
	});
	
	$('#step').on('click', function(e){
						
			calculateNextStep();
					
			refreshGrid();
			
			$tour++;
	
	});
	
	//__ Stop le jeu
	$('#stop').on('click', function(e){
		clearInterval(loop);
		$('#start').show();
		$('#stop').hide();
		
	});
		
});
