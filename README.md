# Boboboibot - statistics assistant of [WerewolfBot](https://telegram.me/werewolfbot) game
instead of visiting http://werewolf.parawuff.com/Home/Stats#, you can list inline in your werewolf role statistic. Not only yours but also members in group.
Even it is not officially part of Werewolfbot, data is crawled from its official website.
[invite the bot to your group!](https://telegram.me/boboibot)

### Command List
- `/setgroup@boboboibot` - Register your self
- `/setgroup listofplayers` - Register your group.  
    Easier while in game after type /players@werewolfbot, copy paste the message after /setgroup e.g  
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

- `/setgame listofplayers` - Register your group.  
    Easier while in game after type /players@werewolfbot, copy paste the message after /setgame  
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
    e.g
    /mostgroup wolf  
    Most 10 common wolf in group:  
    1. Pudy Prima 45.45%  
    2. Mukhammad Ifanto 27.27%  
    3. Rezan 26.67%  
    4. Danang Massandy 25.00%  
    5. Ismail Sunni 25.00%  
    6. Dimas Ciputra 12.50%  
    7. Muhammad Maulana Abdullah 0.00%  
    last update : 28-04-2016 21:12:03  
- `/mostgame role` - top 10 role inside your game order by highest percentage of role (refer to member inside /setgame)  
    result  will be similar as /mostgroup 
- `/randomkill` - while playing with so many users, difficult to decide who has to lynch for first day, so let this bot choose it for you (refer to member inside /setgame)

notes:
- this command is only working if you are in group
- main difference between group and game is `group` command appoint the whole member in your group, `game` command only focus on who are in active game.
- role refers to http://werewolf.parawuff.com/

Have fun !!!
