# BANKOPOLY

## An online webapp to help you play the Bank role in a Monopoly game.

Bankopoly helps you to play a Monopoly game without have to manage the Bank of the game. You don't need to worry about banke-note distributions, players grabing money when you are not looking at the bank ect... You must still have the physical game (board, house cards), but you don't need to distribute the cash sarting amount.

## How it works

Each players must have a smartphone / tablet / computer connected to the internet. One of the player will manage the bank (he will also play the game). When a game start, the initial amount of money is distributed to players (a player can join the game at any time). Each bank transaction (buying a house, paying a tax, own 20K when going on the START case) will be managed by this appliation.

When you use the application as the bank, you will be able to give money at any time to any player. The bank have an initial amount of money, if there is not enough money on the bank, this is the end of the game.
When you play as a player, you can give money from your bank account to any other player or to the bank. If you dont have any more money on your account, you could ask the bank for a credit, or sell cards etc (follow the regular Monopoly rules).

This app just facilitate the bank management, it is not a full online game.

## Prerequisits

APACHE / PHP7 / MYSQL or SQLITE. The `document root` is on the `public` folder of the game. 

You can also use the standalone PHP webserver. Just run it like this :
```
# be sure to be at the ./ folder of the application
$> php -S 0.0.0.0:80 -t ./public/
``` 

To configure a new instance of the game and host it yourself, modify `config/parameters.php` or override params in `config/parameters.extra.php` (this file must not be commited).

To initialize the database, run the script `./src/init.php`.

# Playing

Go to the test instance here : http://bankopoly.forestier.xyz.

## Creating a game

The welcome screen allow you to create a new game, or join an existing game. Click on *create a new game* to start the creation.

On the *Create a new game* screen, you have to enter some information :
- enter the maximum amount of cash in the bank. You can start with 1 000 000 of -M- (-M- is the "mono" currency symbol of money in Monopoly)
- indicate also the initial cash amount when a new player join the game (1500 -M- is the standard value). 
- A random value is generated. It is the game identifier, and players must use it to join a game. Send this game identifier to any player who wants to join this game.
- you can also indicate the name of the player who play also the bank role.
- Then the game can start. As the bank owner, you will be redirected to the *Play* screen.

A game will be available for 24h. After this time, the game is over.

## Joining a game

The welcome screen allow you to join an existing game. Click on *join an existing game* to join in.

On the *Join an existing game* screen, you have to enter some information :
- enter the game identifier (the bank role player must give you this code to enter game)
- enter your name
When you join a game, your bank account will be credited with the initial cash amount, and you will be redirected to the *Play* screen.

## Playing a game

This is the main screen of the application. It allows you to give money to other users (to pay the rent) or the the bank (to pay taxes). For the game to be fast and fun to play, this screen is very simple. When needed :
- enter the amount of money you want to give
- select the player (or the bank) for who you want to send the money
Your account balance is computed in (almost) real time.

If you play as the bank role, you have more options to send money from the bank to other players (each time a player goes thru the Start case, or refund of a tex etc...). Player will receive its money in (almost) real time.


# Technical informations

## Datamodel

The `GAME` table contains information of a running game :
- id : the internal identifier
- game_id : the public game identifier (xxyyyyyc)
- bank_start : the initial amount of money in the bank
- bank_current : the current amount of money in the bank
- date_begin : the datetime when the game start
- bank_player_id : the id of the player who plays the bank role

The `PLAYER` table contains information of player :
- id : the internal identifier of the player
- game_id : the identifier of the game he is playing (public game id)
- name : the name of the player
- current : the current amount of the player account
- date_begin : the datetime when the user join the game

The `OPERATION` table contains history of bank operation :
- id : the internal id of the operation
- date_op : the datetime of the operation
- game_id : the id of the game (public game id)
- from_player_id : the id of the player sending the money (or 0 if it is the bank)
- to_player_id : the id of the player receiving the money (or 0 if it is the bank)
- amount : the amount of the operation (always > 0)

## Source code

### Directory structure
- `.\config\` : all config files
- `.\public\` : all public files served by the webservice (php files, or js/css)
- `.\src\` : PHP code, lib, SQL model
- `.\var\` : log, cache or SQLite data file



