# Boboboibot - statistics assistant of [WerewolfBot](https://telegram.me/werewolfbot) game
instead of visiting http://werewolf.parawuff.com/Home/Stats#, you can list inline in your werewolf role statistic. Not only yours but also members in group.
Even it is not officially part of Werewolfbot, data is crawled from its official website.
[invite the bot to your group!](https://telegram.me/boboibot)

### Command List
- `/setgroup@boboboibot` - Register your self
- `/setgroup listofplayers` - Register your group. easier while in game after type /players@werewolfbot, copy paste the message after /setgroup
e.g
/players@werewolfbot

Werewolf Moderator
Players Alive: 4
Rezan: Alive
Ismail Sunni: Alive
Muhammad Maulana Abdullah: Alive
Danang Massandy: Alive

/setgroup Players Alive: 4
Rezan: Alive
Ismail Sunni: Alive
Muhammad Maulana Abdullah: Alive
Danang Massandy: Alive

- `/setgame listofplayers` - Register your group. easier while in game after type /players@werewolfbot, copy paste the message after /setgame
e.g
/players@werewolfbot

Werewolf Moderator
Players Alive: 4
Rezan: Alive
Ismail Sunni: Alive
Muhammad Maulana Abdullah: Alive
Danang Massandy: Alive

/setgame Players Alive: 4
Rezan: Alive
Ismail Sunni: Alive
Muhammad Maulana Abdullah: Alive
Danang Massandy: Alive

- `/mostgroup role` - top 10 role inside your group order by highest percentage of role (refer to member inside /setgroup)
- `/mostgame role` - top 10 role inside your game order by highest percentage of role (refer to member inside /setgame)
- `/randomkill` - while playing with so many users, difficult to decide who has to lynch for first day, so let this bot choose it for you (refer to member inside /setgame)

notes:
- this command is only working if you are in group
- main difference between group and game is `group` command appoint the whole member in your group, `game` command only focus on who are in active game.
- role refers to http://werewolf.parawuff.com/