connect4
========

CSC309 A3

Stephen Tsimidis (997628161 / g0tsimid)

##Overview of project

###Models
* The project adds a Board_model model, in addition to the data structure and database abstraction models included in the starter code for the Match, User, and Invite tables.
* Board_model: represents the connect4 board as an array of models, with each element in the array including an array which represents one of the columns on the board. Dropping disks into the board causes either a 0 or a 1 to be appended to the corresponding stack.
  * In addition, the $player_turn variable keeps track of which player's turn it is. It can be either the constant Board_model::P1 or Board_model::P2.
* Match
  * I added an initialize_board method, a drop_disk method, and a check_victory_state method. initialize_board is meant to be called when a new match is created.
  * drop_disk is called in response to a dropDisk request in the match controller. It adds a disk to the internal representation of the board in the given column, and returns false if it cannot
  * check_victory_state checks whether the disk at the top of the column passed is part of a four-in-a-row pattern. It makes use of the private method count_consecutive_disks to determine whether there are enough disks in a given diretion.
* User, Invite are the same as the starter code.

###Controllers
* Account
  * this controller was extended to serve a Captcha image from the Securimage library. The createNew handler was modified to check that the image code entered by the user matches this image.
* Board
  * The index page loads information about any games the user is playing, and loads a dummy board if the user is waiting, or the real board if a match is in progress.
  * The getGameBoard serves up just the processed _board_view partial, and is used to update the game board using AJAX.
  * dropDisk attempts to drop a disk onto the game board if it is the user's turn and the game is still active.

###Views
* I refactored the starter code to make use of a template file.
* Since most of the scripts made use of php, the inline scripts for each page were extracted into the _js.php file for each view folder.
* _board_view.php was added as a partial to represent the current game board at any given time. It is meant to be processed by the Board controller and served during AJAX requests for the updated board.
* For each column in the board, a jquery.live handler get associated with the th element so that whenever the user clicks on the element, it submits a dropDisk request to the Board controller.
* Because these th elements get reloaded every time the _board_view gets loaded, a live binding is required in order to re-initialize the handler.


###Libraries
* Securimage was added to the project as a library. No modifications were needed.
