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

Copy the file`config/parameters.php.dist` to `config/pararmeters.php` and edit it. In this file, you can indicate the Database URN, etc...

# Creating a game

The welcome screen allow you to create a new gamae, or join an existing game. Click on *create a new game* to start the creation.

On the *Create a new game* screen, enter the maximum amount of the cash in the bank. You can start with 1 000 000 of -/\/\- (-/\/\- is the currency symbol of money in Monopoly)

