drop database if exists abahubdb;
create database abahubdb;
use abahubdb; 

CREATE TABLE  admini  (
   admin_id  int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
   username  varchar(50) DEFAULT NULL,
   usr_password  varchar(50) DEFAULT NULL,
   email  varchar(50) DEFAULT NULL
);


CREATE TABLE  matches  (
   match_id  int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
   team1_id  int(11) DEFAULT NULL,
   team2_id  int(11) DEFAULT NULL,
   match_date  datetime DEFAULT NULL,
   location  varchar(50) DEFAULT NULL,
   team1_score  int(11) DEFAULT NULL,
   team2_score  int(11) DEFAULT NULL,
   highlight_url  varchar(255) DEFAULT NULL
);

INSERT INTO  matches  ( match_id ,  team1_id ,  team2_id ,  match_date ,  location ,  team1_score ,  team2_score ,  highlight_url ) VALUES
(1, 1, 2, '2024-01-15 19:00:00', 'The Collisium', 85, 80, null),
(2, 3, 4, '2024-01-15 20:30:00', 'City Stadium', 72, 78, null),
(3, 4, 1, '2024-12-16 19:00:00', 'Longshots Court', 85, 120, null),
(4, 2, 5, '2025-01-16 20:30:00', 'Warriors Arena', null, null, null ),
(5, 1, 4, '2024-01-17 19:00:00', 'Main Arena', 90, 82, null),
(6, 3, 5, '2025-01-17 20:30:00', 'Astros Home Court', null, null, null),
(7, 2, 3, '2025-01-25 18:00:00', 'Bay Area', null, null, null),
(8, 5, 1, '2025-01-25 20:00:00', 'The Moon', null, null, null),
(9, 3, 5, '2025-02-01 18:00:00', 'The Hill', null, null, null),
(10, 2, 4, '2025-02-01 20:00:00', 'Bay Area', null, null, null),
(11, 1, 3, '2025-02-08 18:00:00', 'The Collisium', null, null, null),
(12, 5, 3, '2025-02-08 20:00:00', 'The Moon', null, null, null),
(13, 4, 3, '2025-02-15 18:00:00', 'Longshots Court', null, null, null),
(14, 2, 1, '2025-02-15 20:00:00', 'Bay Area', null, null, null),
(15, 5, 2, '2025-02-22 18:00:00', 'The Moon', null, null, null),
(16, 1, 4, '2025-02-22 20:00:00', 'The Collisium', null, null, null);


DELIMITER //
CREATE PROCEDURE GetFeaturedPlayersForPastGame()
BEGIN
    SELECT 
        p.player_id,
        p.Fname,
        p.Lname,
        p.player_image,
        t.team_name,
        t.team_logo,
        SUM(ps.fantasy_points) as total_fantasy_points
    FROM Players p
    JOIN Teams t ON p.team_id = t.team_id
    JOIN PlayerStats ps ON p.player_id = ps.player_id
    JOIN Matches m ON ps.match_id = m.match_id
    WHERE m.match_date < NOW()
    GROUP BY p.team_id
    HAVING total_fantasy_points = (
        SELECT MAX(team_max_points)
        FROM (
            SELECT t2.team_id, SUM(ps2.fantasy_points) as team_max_points
            FROM Players p2
            JOIN PlayerStats ps2 ON p2.player_id = ps2.player_id
            JOIN Matches m2 ON ps2.match_id = m2.match_id
            JOIN Teams t2 ON p2.team_id = t2.team_id
            WHERE m2.match_date < NOW()
            GROUP BY t2.team_id
        ) AS team_points
    )
    ORDER BY total_fantasy_points DESC;
END //
DELIMITER ;

-- Similar procedure for upcoming games
DELIMITER //
CREATE PROCEDURE GetFeaturedPlayersForUpcomingGame()
BEGIN
    SELECT 
        p.player_id, 
        p.Fname,
        p.Lname, 
        p.player_image, 
        t.team_name, 
        t.team_logo,
        AVG(ps.fantasy_points) as avg_fantasy_points
    FROM Players p
    JOIN Teams t ON p.team_id = t.team_id
    JOIN PlayerStats ps ON p.player_id = ps.player_id
    JOIN Matches m ON ps.match_id = m.match_id
    WHERE m.match_date < NOW()
    GROUP BY p.team_id
    HAVING avg_fantasy_points = (
        SELECT MAX(team_avg_points)
        FROM (
            SELECT t2.team_id, AVG(ps2.fantasy_points) as team_avg_points
            FROM Players p2
            JOIN PlayerStats ps2 ON p2.player_id = ps2.player_id
            JOIN Matches m2 ON ps2.match_id = m2.match_id
            JOIN Teams t2 ON p2.team_id = t2.team_id
            WHERE m2.match_date < NOW()
            GROUP BY t2.team_id
        ) AS team_points
    )
    ORDER BY avg_fantasy_points DESC;
END //
DELIMITER ;

CREATE TABLE players (
   player_id INT(11) PRIMARY KEY NOT NULL,
   Fname VARCHAR(50) NOT NULL,
   Lname VARCHAR(50) NOT NULL,
   team_id INT(11) DEFAULT NULL,
   jersey_number VARCHAR(2) DEFAULT NULL,
   position VARCHAR(50) DEFAULT NULL,
   height FLOAT DEFAULT NULL,
   weight FLOAT DEFAULT NULL,
   status ENUM('Active', 'Retired') DEFAULT 'Active',
   bio VARCHAR(255) DEFAULT NULL,
   stats_id INT(11) DEFAULT NULL,
   player_image VARCHAR(255) DEFAULT NULL
);

INSERT INTO players (player_id, Fname, Lname, team_id, jersey_number, position, height, weight, status, bio, stats_id, player_image) VALUES 
(81002026, 'Benson', 'Kas-Vorsah', 1, '13', 'SG', 6.1, 66.33, 'Active', 'aka Kobe, I love basketball and learning new things.', 81002026, "images/player_photos/kobe.png"),
(63272026, 'David ', 'Deng', 5, '19', 'PF', 6.3, 79.0, 'Active', 'I think I am naturally a guard (Haha)', 63272026, "images/player_photos/kd.png"),
(03982025, 'Paa Kwesi', 'Thompson', 4, '1', 'PG', 6.0, 107.63, 'Active', 'No bio available', 03982025, "images/player_photos/cade.png"),
(24942026, 'Darren', 'Andoh', 4, '83', 'PF', 6.2, 106.89, 'Active', 'No bio available', 24942026, "images/player_photos/greene.png"),
(47592027, 'Marc-Etienne', 'Sossou', 5, '0', 'SG', 5.6, 96.9, 'Active', 'aka Mon ami, I love basketball', 47592027, "images/player_photos/westbrook.png"),
(84302027, 'Raymond', 'Appiah', 2, '77', 'PG', 5.9, 63.0, 'Active', 'I play other sports such table tennis and football', 84302027, "images/player_photos/cade.png"),
(99992026, 'Trueman ', 'Mabumbo', 1, '17', 'G', 5.5, 114.54, 'Active', 'No bio available', 99992026, "images/player_photos/kyrie.png"),
(24922027, 'Leslie', 'Tettey', 1, '53', 'SG', 5.6, 96.74, 'Active', 'I love cinema', 24922027, "images/player_photos/booker.png"),
(86772025, 'Elton', 'Gamor', 1, '10', 'G', 6.0, 101.8, 'Active', 'aka. Tino', 86772025, "images/player_photos/cade.png"),
(10952026, 'Sean', 'Yeboah', 1, '24', 'PG', 6.4, 78.03, 'Active', "I'm just a chill guy", 10952026, "images/player_photos/lebron.png"),
(99742026, 'Samuel', 'Annor', 3, '11', 'PG', 6.3, 74.61, 'Active', 'I am the reincarnation of Kyrie Irving', 99742026, "images/player_photos/kyrie.png"),
(69532027, 'Bryan ', 'Achel', 4, '25', 'C', 6.3, 96.39, 'Active', 'Just a chill baller', 69532027, "images/player_photos/kd.png"),
(20422027, 'Nongyin', 'Awindor', 5, '11', 'SF', 6.2, 75.61, 'Active', 'I like shooting 40 foot shots more than free throws', 20422027, "images/player_photos/kevindurant.png"),
(16112023, 'Harry', 'Lamptey', 3, '43', 'C', 6.3, 107.63, 'Retired', 'I love agenda', 16112023, "images/player_photos/greene.png"),
(16382026, 'Samuel', 'Duke', 3, '81', 'SF', 5.11, 94.97, 'Retired', 'Call me Duke Harden', 16382026, "images/player_photos/kyrie.png"),
(23312027, 'Serweh', 'Mike', 3, '69', 'SF', 6.1, 79.8, 'Active', 'No bio available', 23312027, "images/player_photos/booker.png"),
(29572026, 'Nene', 'Quaynortey', 4, '38', 'PF', 6.1, 89.17, 'Active', 'I am a big', 29572026, "images/player_photos/greene.png"),
(68582027, 'Ato', 'Fynn', 5, '15', 'SG', 6.0, 94.11, 'Active', 'I want buckets', 68582027, "images/player_photos/booker.png"),
(29802026, 'Gabriel', 'Gabby', 2, '71', 'SF', 6.1, 72.91, 'Active', 'No bio available', 29802026, "images/player_photos/cade.png"),
(51302027, 'Ademide', 'Fro', 3, '54', 'PG', 5.9, 68.7, 'Active', 'aka. Mide', 51302027, "images/player_photos/kyrie.png"),
(77962027, 'Nigel', 'Big', 2, '84', 'C', 6.4, 86.37, 'Active', 'No bio available', 77962027, "images/player_photos/greene.png"),
(11822026, 'Ernest', 'Smart', 3, '27', 'SG', 6.0, 87.15, 'Active', 'No bio available', 11822026, "images/player_photos/greene.png"),
(82292024, 'Kelvin', 'Tatra', 5, '67', 'PG', 5.7, 88.77, 'Active', 'I am a bucket', 82292024, "images/player_photos/westbrook.png"),
(66942023, 'Prince', 'Yeboah', 3, '26', 'PF', 6.3, 100.46, 'Retired', 'aka. Money Boy. I am the best fifa player in Ashesi', 66942023, "images/player_photos/kd.png"),
(11272024, 'Kwaku', 'Lopez', 5, '3', 'C', 6.4, 107.08, 'Retired', 'Shooting Big', 11272024, "images/player_photos/greene.png"),
(19182024, 'Ernest', 'Double T', 5, '60', 'SG', 6.1, 84.48, 'Retired', 'DoubleT', 19182024, "images/player_photos/cade.png"),
(21082024, 'Dawud', 'Curry', 5, '84', 'SG', 6.0, 110.61, 'Retired', 'No bio available', 21082024, "images/player_photos/curry.png"),
(55232025, 'Sugri', 'Amaleboba', 4, '6', 'SG', 6.0, 67.55, 'Active', 'aka Suuu', 55232025, "images/player_photos/curry.png"),
(49272025, 'Ayo', 'Balima', 4, '81', 'PF', 6.2, 65.93, 'Active', 'No bio available', 49272025, "images/player_photos/westbrook.png"),
(52112026, 'Boss', 'Baeta', 4, '91', 'PG', 6.0, 80.39, 'Active', 'I am speed', 52112026, "images/player_photos/cade.png"),
(11182024, 'David ', 'Quaynor', 2, '80', 'PF', 6.2, 91.18, 'Retired', 'aka. Icy', 11182024, "images/player_photos/booker.png"),
(14802024, 'Jalil', 'Thomas', 2, '36', 'SF', 6.1, 67.51, 'Active', 'No bio available', 14802024, "images/player_photos/westbrook.png"),
(58662024, 'Kofi', 'Menka', 2, '1', 'PG', 5.9, 79.82, 'Retired', 'No bio available', 58662024, null),
(25022025, 'Ronel', 'Roni', 2, '22', 'SG', 6.1, 79.75, 'Active', 'No bio available', 25022025, "images/player_photos/cade.png"),
(15602025, 'Papa', 'Yaw', 2, '17', 'C', 6.4, 105.12, 'Active', 'I am a wrestler', 15602025, "images/player_photos/greene.png"),
(41072024, 'Nana-Kweku', 'Djan', 2, '74', 'SG', 5.9, 116.61, 'Retired', 'aka. NKD', 41072024, "images/player_photos/curry.png"),
(94282023, 'Ayeyi', 'Djan', 4, '2', 'SG', 5.8, 65.89, 'Retired', 'aka. AD Breezy', 94282023, "images/player_photos/kyrie.png"),
(10922023, 'Stone', 'Su', 4, '55', 'Coach', 5.7, 82.6, 'Retired', 'No bio available', 10922023, null),
(65892025, 'Frankie', 'Pop', 4, '95', 'Coach', 5.7, 71.2, 'Active', 'No bio available', 65892025, null),
(76122024, 'Hannibal', 'Han', 2, '53', 'Coach', 5.9, 67.84, 'Retired', 'Immovable Object', 76122024, null),
(67092026, 'Nkunim', 'Ntim', 1, '83', 'Coach', 6.0, 93.17, 'Active', 'No bio available', 67092026, null),
(37832024, 'Wepea', 'Webs', 5, '69', 'Coach', 5.8, 68.12, 'Retired', 'Webs', 37832024, null),
(26212025, 'Etornam', 'Awoye', 2, '3', 'PF', 6.4, 72.88, 'Active', 'aka. Midie, ', 26212025, null),
(78392024, 'David ', 'Mustashio', 5, '50', 'SF', 6.1, 80.28, 'Retired', 'Floor General', 78392024, null),
(59822023, 'Papa', 'Kwame', 4, '29', 'PG', 5.9, 109.28, 'Retired', 'aka. Point god, shootrr', 59822023, null),
(60842026, 'Bernard', 'Ben', 1, '4', 'PG', 5.9, 97.33, 'Active', 'No bio available', 60842026, null),
(45242023, 'Kelvin', 'Edem', 1, '17', 'PG', 5.9, 68.0, 'Retired', 'aka. Kvems, Roomie', 45242023, null),
(43272025, 'Pascal', 'Pivot', 1, '56', 'SF', 6.2, 71.83, 'Active', 'No bio available', 43272025, null),
(63562024, 'Perry', 'Tyler', 1, '55', 'SF', 6.1, 116.03, 'Retired', 'No bio available', 63562024, null),
(79992026, 'Arnold', 'Senam', 4, '15', 'C', 6.3, 60.16, 'Active', 'Stretch Big', 79992026, null),
(66012024, 'Asare', 'Asa', 4, '14', 'C', 6.3, 92.24, 'Retired', 'No bio available', 66012024, null),
(71962027, 'Awoye', 'Aw', 2, '17', 'SF', 5.11, 91.28, 'Active', 'No bio available', 71962027, null),
(76622025, 'Fadda', 'Fa', 4, '18', 'SF', 5.11, 118.19, 'Retired', 'No bio available', 76622025, null),
(70562025, 'Gerhard', 'Gerh', 1, '17', 'PF', 6.2, 73.78, 'Retired', 'No bio available', 70562025, null),
(23682024, 'John ', 'Adjei', 4, '59', 'PF', 6.2, 104.2, 'Retired', 'No bio available', 23682024, null),
(98852023, 'Lawer', 'Law', 4, '76', 'PF', 6.3, 70.96, 'Retired', "Ashesi's Strongest", 98852023, null),
(52202024, 'Roland', 'Rol', 2, '85', 'C', 6.3, 117.17, 'Retired', 'No bio available', 52202024, null),
(50302024, 'Kotaa', 'Kwame', 2, '76', 'SG', 5.9, 106.26, 'Retired', 'No bio available', 50302024, null),
(14492025, 'Jeffrey', 'Jeff', 5, '24', 'PF', 6.2, 102.95, 'Active', 'No bio available', 14492025, null),
(62492027, 'Ajak', 'Baah', 4, '38', 'PF', 6.3, 73.54, 'Active', 'No bio available', 62492027, null),
(55222026, 'Moses', 'Kelvin', 2, '89', 'SF', 6.1, 118.44, 'Active', 'Walking Bucket', 55222026, null);



CREATE TABLE  playerstats  (
   stats_id  int(11) PRIMARY KEY NOT NULL,
   player_id  int(11) DEFAULT NULL,
   match_id int(11),
   points  int(11) DEFAULT 0,
   PPG  int(11) DEFAULT 0,
   assists  int(11) DEFAULT 0,
   APG  int(11) DEFAULT 0,
   rebounds  int(11) DEFAULT 0,
   RPG  int(11) DEFAULT 0,
   steals  int(11) DEFAULT 0,
   SPG  int(11) DEFAULT 0,
   blocks  int(11) DEFAULT 0,
   BPG  int(11) DEFAULT 0,
   three_pointers int(11) DEFAULT 0,
   free_throws int(11) DEFAULT 0,
   fantasy_points int(11) DEFAULT 0,
   games_played  int(11) DEFAULT 0,
   FOREIGN KEY (match_id) REFERENCES matches(match_id)
);

INSERT INTO  playerstats  ( stats_id ,  player_id , match_id, points ,  PPG,  assists,  APG,  rebounds,  RPG,  steals,  SPG,  blocks,  BPG, three_pointers, free_throws,  games_played) VALUES
(1, 81002026, 5, 150, 25, 45, 3, 85, 6, 19, 2, 11, 1, 45, 33, 8),
(2, 03982025, 6, 80, 18, 133, 2, 34, 5, 28, 3, 10, 1, 20, 11, 8),
(3, 24942026, 7, 100, 18, 13, 2, 34, 5, 18, 3, 10, 1, 20, 15, 8),
(4, 99992026, 9, 10, 18, 13, 2, 34, 5, 18, 3, 10, 1, 20, 11, 8),
(5, 86772025, 11, 110, 18, 13, 2, 34, 5, 18, 3, 10, 1, 20, 11, 8),
(6, 10952026, 5, 115, 18, 13, 2, 34, 5, 18, 3, 10, 1, 20, 11, 8),
(7, 99742026, 7, 100, 18, 13, 2, 34, 5, 298, 3, 20, 1, 20, 11, 8),
(8, 69532027, 8, 106, 18, 13, 2, 34, 5, 18, 3, 14, 1, 20, 17, 8),
(9, 63272026, 7, 107, 18, 13, 2, 34, 5, 18, 3, 10, 1, 20, 11, 8),
(10, 20422027, 5, 108, 18, 13, 2, 34, 5, 18, 3, 10, 1, 20, 11, 8),
(11, 84302027, 2, 104, 18, 13, 2, 34, 5, 18, 3, 10, 1, 20, 11, 8),
(12, 24922027, 5, 89, 18, 13, 2, 34, 5, 18, 3, 15, 1, 30, 18, 8),
(13, 47592027, 8, 120, 18, 13, 2, 34, 5, 13, 3, 10, 1, 20, 11, 8),
(14, 55232025, 3, 130, 18, 13, 2, 34, 5, 18, 3, 10, 1, 20, 51, 8),
(15, 49272025, 4, 112, 18, 13, 2, 134, 5, 18, 3, 6, 1, 20, 11, 8),
(16, 52112026, 6, 123, 18, 13, 2, 34, 5, 18, 3, 12, 1, 30, 11, 8),
(17, 11182024, 8, 102, 18, 13, 2, 34, 5, 15, 3, 10, 1, 20, 31, 8),
(18, 14802024, 12, 104, 18, 13, 2, 34, 5, 18, 3, 10, 1, 25, 11, 8),
(19, 25022025, 15, 102, 18, 13, 2, 43, 5, 18, 3, 10, 1, 20, 11, 8),
(20, 15602025, 14, 105, 18, 13, 2, 24, 5, 18, 3, 13, 1, 20, 21, 8),
(21, 41072024, 16, 102, 18, 13, 2, 34, 5, 21, 3, 10, 1, 20, 11, 8),
(22, 26212025, 10, 105, 18, 13, 2, 34, 5, 18, 3, 17, 1, 20, 21, 8),
(23, 60842026, 5, 106, 18, 13, 2, 34, 5, 18, 3, 10, 1, 22, 31, 8),
(24, 55222026, 11, 108, 18, 13, 2, 34, 5, 18, 3, 10, 1, 20, 31, 8),
(25, 62492027, 10, 107, 18, 13, 2, 34, 5, 18, 3, 10, 1, 20, 41, 8),
(26, 14492025, 9, 107, 18, 13, 2, 34, 5, 18, 3, 10, 1, 24, 11, 8),
(27, 50302024, 6, 109, 18, 13, 2, 34, 5, 18, 3, 10, 1, 20, 1, 8),
(29, 43272025, 7, 100, 18, 13, 2, 34, 5, 18, 3, 10, 1, 25, 3, 8),
(30, 79992026, 8, 109, 18, 13, 2, 34, 5, 18, 3, 10, 1, 20, 8, 8);


-- Update calculation procedure to calculate fantasy points
DELIMITER //
CREATE PROCEDURE UpdatePlayerFantasyPoints()
BEGIN
    UPDATE playerstats
    SET fantasy_points = points + assists + rebounds + steals + blocks + three_pointers + free_throws;
END //
DELIMITER ;

-- Call the procedure to update fantasy points
CALL UpdatePlayerFantasyPoints();

CREATE TABLE  teams  (
   team_id  int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
   team_name  varchar(50) NOT NULL,
   team_logo  varchar(255) DEFAULT NULL,
   wins  int(11) DEFAULT 0,
   losses  int(11) DEFAULT 0,
   games_played  int(11) DEFAULT 0,
   diff  varchar(50) DEFAULT '0',
   ranking  int(11) DEFAULT NULL,
   championships int DEFAULT 0,
   points_total INT DEFAULT 0,
   assists_total INT DEFAULT 0,
   rebounds_total INT DEFAULT 0,
   steals_total INT DEFAULT 0,
   blocks_total INT DEFAULT 0,
   ppg_rank INT,
   apg_rank INT,
   rpg_rank INT,
   spg_rank INT,
   bpg_rank INT,
   rooster_id  int(11) DEFAULT NULL,
   coach_id INT,
   captain_id INT,
   assistant_coach_id INT,
   FOREIGN KEY (coach_id) REFERENCES players(player_id),
   FOREIGN KEY (captain_id) REFERENCES players(player_id),
   FOREIGN KEY (assistant_coach_id) REFERENCES players(player_id)
);


INSERT INTO  teams (team_id,  team_name,  team_logo,  wins,  losses,  games_played,  diff,  ranking, championships, rooster_id, coach_id, captain_id, assistant_coach_id ) VALUES
(1, 'AshKnights', 'images/team_logos/Ash_Knights_Logo.png', 6, 2, 8, '+32', 2, 1, 1, 67092026, 81002026, 86772025),
(2, 'Berekuso Warriors', 'images/team_logos/Berekuso_Warriors_logo_edited.png', 7, 1, 8, '+19', 1, 1, 2, 76122024, null, null),
(3, 'HillBlazers', 'images/team_logos/HillBlazers_logo_edited.png', 0, 8, 8, '-32', 5, 0, 3, 65892025, 29572026, null),
(4, 'LongShots', 'images/team_logos/Longshots_Logo.png', 5, 3, 8, '+12', 3, 1, 4, 10922023, 49272025, null),
(5, 'Los Astros', 'images/team_logos/Los_Astros_Logo_edited.png', 2, 6, 8, '-10', 4, 0, 5, 37832024, 69532027, null);

ALTER TABLE teams
ADD COLUMN team_color VARCHAR(10) DEFAULT '#f0f0f0',
ADD COLUMN text_color VARCHAR(10) DEFAULT '#000000';

UPDATE teams SET team_color = '#38393d', text_color = '#FFFFFF' WHERE team_name = 'AshKnights';
UPDATE teams SET team_color = '#f3da38', text_color = '#FFFFFF' WHERE team_name = 'Berekuso Warriors';
UPDATE teams SET team_color = '#18d507', text_color = '#FFFFFF' WHERE team_name = 'HillBlazers';  
UPDATE teams SET team_color = '#d81734', text_color = '#FFFFFF' WHERE team_name = 'Longshots';
UPDATE teams SET team_color = '#7f07d5', text_color = '#FFFFFF' WHERE team_name = 'Los Astros';


CREATE TABLE championships (
    defendingChampionId INT PRIMARY KEY AUTO_INCREMENT,
    team_id INT,
    championship_year INT,
    FOREIGN KEY (team_id) REFERENCES teams(team_id)
);

INSERT INTO championships (defendingChampionId, team_id, championship_year) VALUES
(4, 1, 2024),
(3, 2, 2023),
(2, 4, 2022),
(1, 4, 2021);

CREATE TABLE  teamroster  (
   teamRoster_id  int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
   team_id  int(11) DEFAULT NULL,
   coach_id  int(11) DEFAULT NULL,
   player1_id  int(11) DEFAULT NULL,
   player2_id  int(11) DEFAULT NULL,
   player3_id  int(11) DEFAULT NULL,
   player4_id  int(11) DEFAULT NULL,
   player5_id  int(11) DEFAULT NULL,
   player6_id  int(11) DEFAULT NULL,
   player7_id  int(11) DEFAULT NULL,
   player8_id  int(11) DEFAULT NULL,
   player9_id  int(11) DEFAULT NULL,
   player10_id  int(11) DEFAULT NULL
);

INSERT INTO  teamroster  ( teamRoster_id ,  team_id ,  coach_id ,  player1_id ,  player2_id ,  player3_id ,  player4_id ,  player5_id ,  player6_id ,  player7_id ,  player8_id ,  player9_id ,  player10_id ) VALUES
(1, 1, 1, 81002026, 99992026, 10952026, 86772025, 43272025, 60842026, 45242023, 63562024, NULL, NULL),
(2, 2, 2, 11182024, 58662024, 25022025, 14802024, 15602025, 99742026, NULL, NULL, NULL, NULL),
(3, 3, 3, 16382026, 16112023, 66942023, 24942026, 84302027, 50302024, 55222026, 29572026, 76622025, 20422027),
(4, 4, 4, 03982025, 55232025, 49272025, 52112026, 94282023, 59822023, 79992026, 14492025, 62492027, 98852023),
(5, 5, 5, 47592027, 69532027, 68582027, 11272024, 19182024, 21082024, 37832024, 78392024, NULL, NULL);

ALTER TABLE teams 
ADD COLUMN is_defending_champion BOOLEAN DEFAULT FALSE;

CREATE TABLE  teamstats  (
   team_stats_id  int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
   team_id  int(11) DEFAULT NULL,
   PPG  int(11) DEFAULT NULL,
   APG  int(11) DEFAULT NULL,
   RPG  int(11) DEFAULT NULL,
   SPG  int(11) DEFAULT NULL,
   BPG  int(11) DEFAULT NULL
);

DELIMITER //

CREATE PROCEDURE CalculateTeamStatsRankings()
BEGIN
    -- Calculate total points
    UPDATE teams t
    JOIN (
        SELECT team_id, SUM(points) as total_points
        FROM playerstats ps
        JOIN players p ON ps.player_id = p.player_id
        GROUP BY team_id
    ) team_points ON t.team_id = team_points.team_id
    SET t.points_total = team_points.total_points;

    -- Calculate total assists
    UPDATE teams t
    JOIN (
        SELECT team_id, SUM(assists) as total_assists
        FROM playerstats ps
        JOIN players p ON ps.player_id = p.player_id
        GROUP BY team_id
    ) team_assists ON t.team_id = team_assists.team_id
    SET t.assists_total = team_assists.total_assists;

    -- Calculate total rebounds
    UPDATE teams t
    JOIN (
        SELECT team_id, SUM(rebounds) as total_rebounds
        FROM playerstats ps
        JOIN players p ON ps.player_id = p.player_id
        GROUP BY team_id
    ) team_rebounds ON t.team_id = team_rebounds.team_id
    SET t.rebounds_total = team_rebounds.total_rebounds;

    -- Calculate total steals
    UPDATE teams t
    JOIN (
        SELECT team_id, SUM(steals) as total_steals
        FROM playerstats ps
        JOIN players p ON ps.player_id = p.player_id
        GROUP BY team_id
    ) team_steals ON t.team_id = team_steals.team_id
    SET t.steals_total = team_steals.total_steals;

    -- Calculate total blocks
    UPDATE teams t
    JOIN (
        SELECT team_id, SUM(blocks) as total_blocks
        FROM playerstats ps
        JOIN players p ON ps.player_id = p.player_id
        GROUP BY team_id
    ) team_blocks ON t.team_id = team_blocks.team_id
    SET t.blocks_total = team_blocks.total_blocks;

    -- Rank PPG
    SET @rank = 0;
    UPDATE teams t
    JOIN (
        SELECT team_id, 
               @rank := @rank + 1 as ppg_ranking
        FROM teams 
        ORDER BY points_total / games_played DESC
    ) ranked ON t.team_id = ranked.team_id
    SET t.ppg_rank = ranked.ppg_ranking;

    -- Rank APG
    SET @rank = 0;
    UPDATE teams t
    JOIN (
        SELECT team_id, 
               @rank := @rank + 1 as apg_ranking
        FROM teams 
        ORDER BY assists_total / games_played DESC
    ) ranked ON t.team_id = ranked.team_id
    SET t.apg_rank = ranked.apg_ranking;

    -- Rank RPG
    SET @rank = 0;
    UPDATE teams t
    JOIN (
        SELECT team_id, 
               @rank := @rank + 1 as rpg_ranking
        FROM teams 
        ORDER BY rebounds_total / games_played DESC
    ) ranked ON t.team_id = ranked.team_id
    SET t.rpg_rank = ranked.rpg_ranking;

    -- Rank SPG
    SET @rank = 0;
    UPDATE teams t
    JOIN (
        SELECT team_id, 
               @rank := @rank + 1 as spg_ranking
        FROM teams 
        ORDER BY steals_total / games_played DESC
    ) ranked ON t.team_id = ranked.team_id
    SET t.spg_rank = ranked.spg_ranking;

    -- Rank BPG
    SET @rank = 0;
    UPDATE teams t
    JOIN (
        SELECT team_id, 
               @rank := @rank + 1 as bpg_ranking
        FROM teams 
        ORDER BY blocks_total / games_played DESC
    ) ranked ON t.team_id = ranked.team_id
    SET t.bpg_rank = ranked.bpg_ranking;
END //

DELIMITER ;

-- Call the procedure to update stats
CALL CalculateTeamStatsRankings();

INSERT INTO  teamstats  ( team_stats_id ,  team_id ,  PPG ,  APG ,  RPG ,  SPG ,  BPG ) VALUES
(1, 1, 95, 16, 35, 9, 8),
(2, 2, 92, 16, 36, 9, 8),
(3, 3, 80, 15, 34, 8, 8),
(4, 4, 85, 15, 35, 8, 8),
(5, 5, 75, 15, 33, 8, 7);

-- Table for Users
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    username varchar(50) NOT NULL UNIQUE,
    gender ENUM('Male', 'Female') NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('ABA Star', 'ABA Fan') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fav_team ENUM ('AshKnights', 'Berekuso Warriors', 'HillBlazers', 'Longshots', 'Los Astros')
);

CREATE TABLE awards (
   award_id INT PRIMARY KEY,
   award_name VARCHAR(100),
   award_image VARCHAR(255)
);

CREATE TABLE winners (
   winner_id INT PRIMARY KEY AUTO_INCREMENT,
   award_id INT,
   player_id INT,
   times_won INT,
   season_year INT,
   FOREIGN KEY (award_id) REFERENCES awards(award_id),
   FOREIGN KEY (player_id) REFERENCES players(player_id)
);

CREATE TABLE team_winners (
   team_winner_id INT PRIMARY KEY AUTO_INCREMENT,
   award_id INT,
   team_id INT,
   times_won INT,
   season_year INT,
   FOREIGN KEY (award_id) REFERENCES awards(award_id),
   FOREIGN KEY (team_id) REFERENCES teams(team_id)
);

-- Populate Awards Table
INSERT INTO awards (award_id, award_name, award_image) VALUES
-- Individual Awards
(1, 'MVP', 'Awards/MVPtrophy.png'),
(2, 'Finals MVP', 'Awards/FinalsMVPtrophy.png'),
(3, 'WC MVP', 'Awards/WCMVP.png'),
(4, 'EC MVP', 'Awards/ECMVP.png'),
(5, 'MIP', 'Awards/MIPtrophy.png'),
(6, 'DPOY', 'Awards/DPOYtrophy.png'),
(7, 'ROTY', 'Awards/ROTYtrophy.png'),
(8, 'SMOTY', 'Awards/SixthMOTYtrophy.png'),
(9, 'Coach of the Year', 'Awards/CoachOTY.png'),
(10, 'Clutch Player of the Year', 'Awards/ClutchPOTYtrophy.png'),
(11, 'ABA Executive of the Year', 'Awards/ExecutiveOTY.png'),
(12, 'ABA Sportsmanship Award', 'Awards/SportsmanshipAward.png'),
(13, 'Best Teammate of the Year', 'Awards/TeammateAward.png'),
(14, 'All Star MVP', 'Awards/AllstarMVP.png'),
(15, '3pt Contest Winner', 'Awards/3ptContestWinner.png'),
(16, 'King of the Court', 'Awards/KingOTC.png'),
(17, 'Le Champ', 'Awards/LeChamp.png'),
(18, 'Clutch Challenge Award', 'Awards/ClutchChallengeAward.png'),
(19, 'Inter Class MVP', 'Awards/InterClassMVP.png'),
(20, 'ABA Citizenship Award', 'Awards/CitizenshipAward.png'),
(21, 'ABA Hustle Player Award', 'Awards/HustleAward.png'),
(22, 'Scoring Champ', 'Awards/scoring-champ.jpg'),
(23, 'Assists Champ', 'Awards/assists-champ.jpg'),
(24, 'Rebounds Champ', 'Awards/rebounds-champ.jpg'),
(25, 'Steals Champ', 'Awards/steals-champ.jpg'),
(26, 'Blocks Champ', 'Awards/blocks-champ.jpg'),
(27, '3 Point Champ', 'Awards/3ptContestWinner.png'),

-- Team Awards
(28, 'ABA Champions', 'Awards/FinalsTrophy.png'),
(29, 'WC Champions', 'Awards/WCtrophy.png'),
(30, 'EC Champions', 'Awards/ECtrophy.png'),
(31, '1 Seed Champions', 'Awards/1stSeedAward.png'),
(32, 'Inter Class Champs', 'Awards/InterClassChamps.png'),
(33, 'All Star Selection', 'Awards/AllStarSele.jpg'),
(34, 'School Team Selection', 'Awards/SchoolTeamSele.jpg'),

-- Team-based Individual Awards
(35, 'All ABA First Team', 'Awards/all-league-team.jpg'),
(36, 'All ABA Second Team', 'Awards/all-league-team.jpg'),
(37, 'All ABA Defensive Team', 'Awards/defensive-team.jpg'),
(38, 'All Rookie First Team', 'Awards/rookie-team.jpg'),
(39, 'All WABA First Team', 'Awards/waba-team.jpg');

-- Populate Winners Table (Individual Awards)
INSERT INTO winners (award_id, player_id, times_won, season_year) VALUES
-- Sample individual award winners
((SELECT award_id FROM awards WHERE award_name = 'MVP'), 
 (SELECT player_id FROM players WHERE Lname = 'Kas-Vorsah'), 
 2, 2024),
((SELECT award_id FROM awards WHERE award_name = 'Finals MVP'), 
 (SELECT player_id FROM players WHERE Lname = 'Kas-Vorsah'), 
 2, 2024),
 ((SELECT award_id FROM awards WHERE award_name = 'WC MVP'), 
 (SELECT player_id FROM players WHERE Lname = 'Kas-Vorsah'), 
 2, 2024),
  ((SELECT award_id FROM awards WHERE award_name = 'EC MVP'), 
 (SELECT player_id FROM players WHERE Fname = 'Sugri'), 
 2, 2024),
 ((SELECT award_id FROM awards WHERE award_name = 'MIP'), 
 (SELECT player_id FROM players WHERE Lname = 'Kas-Vorsah'), 
 2, 2024),
 ((SELECT award_id FROM awards WHERE award_name = 'DPOY'), 
 (SELECT player_id FROM players WHERE Lname = 'Baeta'), 
 2, 2024), 
((SELECT award_id FROM awards WHERE award_name = 'ROTY'), 
 (SELECT player_id FROM players WHERE Fname = 'Sean'), 
 1, 2023),
 ((SELECT award_id FROM awards WHERE award_name = 'SMOTY'), 
 (SELECT player_id FROM players WHERE Lname = 'Kas-Vorsah'), 
 2, 2024),
 ((SELECT award_id FROM awards WHERE award_name = 'Coach of the Year'), 
 (SELECT player_id FROM players WHERE Lname = 'Ntim'), 
 2, 2024),
 ((SELECT award_id FROM awards WHERE award_name = 'Clutch Player of the Year'), 
 (SELECT player_id FROM players WHERE Lname = 'Kas-Vorsah'), 
 2, 2024),
 ((SELECT award_id FROM awards WHERE award_name = 'ABA Executive of the Year'), 
 (SELECT player_id FROM players WHERE Lname = 'Lamptey'), 
 2, 2024),
 ((SELECT award_id FROM awards WHERE award_name = 'ABA Sportsmanship Award'), 
 (SELECT player_id FROM players WHERE Fname = 'Etornam'), 
 2, 2024), 
 ((SELECT award_id FROM awards WHERE award_name = 'Best Teammate of the Year'), 
 (SELECT player_id FROM players WHERE Fname = 'Elton'), 
 2, 2024),
 ((SELECT award_id FROM awards WHERE award_name = 'All Star MVP'), 
 (SELECT player_id FROM players WHERE Lname = 'Kas-Vorsah'), 
 2, 2024),
 ((SELECT award_id FROM awards WHERE award_name = '3pt Contest Winner'), 
 (SELECT player_id FROM players WHERE Lname = 'Kas-Vorsah'), 
 2, 2024),
 ((SELECT award_id FROM awards WHERE award_name = 'King of the Court'), 
 (SELECT player_id FROM players WHERE Fname = 'Prince'), 
 2, 2024),
 ((SELECT award_id FROM awards WHERE award_name = 'Le Champ'), 
 (SELECT player_id FROM players WHERE Fname = 'Sugri'), 
 2, 2024),
 ((SELECT award_id FROM awards WHERE award_name = 'Clutch Challenge Award'), 
 (SELECT player_id FROM players WHERE Lname = 'Mabumbo'), 
 2, 2024),
 ((SELECT award_id FROM awards WHERE award_name = 'Inter Class MVP'), 
 (SELECT player_id FROM players WHERE Lname = 'Trueman'), 
 2, 2024),
 ((SELECT award_id FROM awards WHERE award_name = 'ABA Citizenship Award'), 
 (SELECT player_id FROM players WHERE Fname = 'Kas-Vorsah'), 
 2, 2024),
 ((SELECT award_id FROM awards WHERE award_name = 'ABA Hustle Player Award'), 
 (SELECT player_id FROM players WHERE Lname = 'Kas-Vorsah'), 
 2, 2024),
 ((SELECT award_id FROM awards WHERE award_name = 'Scoring Champ'), 
 (SELECT player_id FROM players WHERE Lname = 'Kas-Vorsah'), 
 2, 2024),
((SELECT award_id FROM awards WHERE award_name = 'Assists Champ'), 
 (SELECT player_id FROM players WHERE Lname = 'Kas-Vorsah'), 
 2, 2024),
((SELECT award_id FROM awards WHERE award_name = 'Rebounds Champ'), 
 (SELECT player_id FROM players WHERE Lname = 'Kas-Vorsah'), 
 2, 2024),
 ((SELECT award_id FROM awards WHERE award_name = 'Steals Champ'), 
 (SELECT player_id FROM players WHERE Lname = 'Kas-Vorsah'), 
 2, 2024),
 ((SELECT award_id FROM awards WHERE award_name = 'Blocks Champ'), 
 (SELECT player_id FROM players WHERE Lname = 'Kas-Vorsah'), 
 2, 2024),
 ((SELECT award_id FROM awards WHERE award_name = '3 Point Champ'), 
 (SELECT player_id FROM players WHERE Lname = 'Kas-Vorsah'), 
 2, 2024);

-- Populate Team Winners Table
INSERT INTO team_winners (award_id, team_id, times_won, season_year) VALUES
-- Sample team award winners
((SELECT award_id FROM awards WHERE award_name = 'ABA Champions'), 
 (SELECT team_id FROM teams WHERE team_name = 'AshKnights'), 
 2, 2024),
((SELECT award_id FROM awards WHERE award_name = 'WC Champions'), 
 (SELECT team_id FROM teams WHERE team_name = 'AshKnights'), 
 2, 2024),
((SELECT award_id FROM awards WHERE award_name = 'EC Champions'), 
 (SELECT team_id FROM teams WHERE team_name = 'LongShots'), 
 1, 2024),
((SELECT award_id FROM awards WHERE award_name = '1 Seed Champions'), 
 (SELECT team_id FROM teams WHERE team_name = 'Berekuso Warriors'), 
 1, 2024),
((SELECT award_id FROM awards WHERE award_name = 'Inter Class Champs'), 
 (SELECT team_id FROM teams WHERE team_name = 'Los Astros'), 
 1, 2024),
((SELECT award_id FROM awards WHERE award_name = 'All Star Selection'), 
 (SELECT team_id FROM teams WHERE team_name = 'AshKnights'), 
 1, 2024),
 ((SELECT award_id FROM awards WHERE award_name = 'School Team Selection'), 
 (SELECT team_id FROM teams WHERE team_name = 'AshKnights'), 
 1, 2024),
 ((SELECT award_id FROM awards WHERE award_name = 'All ABA First Team'), 
 (SELECT team_id FROM teams WHERE team_name = 'AshKnights'), 
 1, 2024),
((SELECT award_id FROM awards WHERE award_name = 'All ABA Second Team'), 
 (SELECT team_id FROM teams WHERE team_name = 'Berekuso Warriors'), 
 1, 2024),
((SELECT award_id FROM awards WHERE award_name = 'All ABA Defensive Team'), 
 (SELECT team_id FROM teams WHERE team_name = 'AshKnights'), 
 1, 2024),
((SELECT award_id FROM awards WHERE award_name = 'All Rookie First Team'), 
 (SELECT team_id FROM teams WHERE team_name = 'Los Astros'), 
 1, 2024),
((SELECT award_id FROM awards WHERE award_name = 'All WABA First Team'), 
 (SELECT team_id FROM teams WHERE team_name = 'HillBlazers'), 
 1, 2024);



CREATE TABLE playoff_brackets (
	bracket_id INT AUTO_INCREMENT PRIMARY KEY,
	first_seed_id INT,
	fourth_seed_id INT,
	second_seed_id INT,
	third_seed_id INT,
	FOREIGN KEY (first_seed_id) REFERENCES teams(team_id),
	FOREIGN KEY (fourth_seed_id) REFERENCES teams(team_id),
	FOREIGN KEY (second_seed_id) REFERENCES teams(team_id),
	FOREIGN KEY (third_seed_id) REFERENCES teams(team_id)
);

INSERT INTO playoff_brackets (first_seed_id, fourth_Seed_id, second_seed_id, third_seed_id)
SELECT 
	(SELECT team_id FROM teams ORDER BY wins DESC LIMIT 1) as first_seed,
	(SELECT team_id FROM teams ORDER BY wins DESC LIMIT 1 OFFSET 3) as fourth_seed,
	(SELECT team_id FROM teams ORDER BY wins DESC LIMIT 1 OFFSET 1) as second_seed,
	(SELECT team_id FROM teams ORDER BY wins DESC LIMIT 1 OFFSET 2) as third_seed

DELIMITER //

CREATE PROCEDURE CalculateTeamStats()
BEGIN
    -- Calculate total stats for each team
    DROP TEMPORARY TABLE IF EXISTS team_total_stats;
    CREATE TEMPORARY TABLE team_total_stats AS (
        SELECT 
            p.team_id,
            SUM(ps.points) as total_points,
            SUM(ps.assists) as total_assists,
            SUM(ps.rebounds) as total_rebounds,
            SUM(ps.steals) as total_steals,
            SUM(ps.blocks) as total_blocks,
            COUNT(DISTINCT ps.player_id) as players_count
        FROM players p
        JOIN playerstats ps ON p.player_id = ps.player_id
        GROUP BY p.team_id
    );

    -- Rank teams based on per-game and total stats
    -- PPG Rank
    SET @ppg_rank = 0;
    UPDATE teams t
    JOIN (
        SELECT 
            team_id, 
            @ppg_rank := @ppg_rank + 1 AS rank
        FROM (
            SELECT 
                team_id, 
                total_points / games_played AS avg_points
            FROM teams 
            JOIN team_total_stats USING (team_id)
            ORDER BY avg_points DESC
        ) ranked
    ) ppg ON t.team_id = ppg.team_id
    SET t.ppg_rank = ppg.rank;

    -- APG Rank
    SET @apg_rank = 0;
    UPDATE teams t
    JOIN (
        SELECT 
            team_id, 
            @apg_rank := @apg_rank + 1 AS rank
        FROM (
            SELECT 
                team_id, 
                total_assists / games_played AS avg_assists
            FROM teams 
            JOIN team_total_stats USING (team_id)
            ORDER BY avg_assists DESC
        ) ranked
    ) apg ON t.team_id = apg.team_id
    SET t.apg_rank = apg.rank;

    -- RPG Rank
    SET @rpg_rank = 0;
    UPDATE teams t
    JOIN (
        SELECT 
            team_id, 
            @rpg_rank := @rpg_rank + 1 AS rank
        FROM (
            SELECT 
                team_id, 
                total_rebounds / games_played AS avg_rebounds
            FROM teams 
            JOIN team_total_stats USING (team_id)
            ORDER BY avg_rebounds DESC
        ) ranked
    ) rpg ON t.team_id = rpg.team_id
    SET t.rpg_rank = rpg.rank;

    -- SPG Rank
    SET @spg_rank = 0;
    UPDATE teams t
    JOIN (
        SELECT 
            team_id, 
            @spg_rank := @spg_rank + 1 AS rank
        FROM (
            SELECT 
                team_id, 
                total_steals / games_played AS avg_steals
            FROM teams 
            JOIN team_total_stats USING (team_id)
            ORDER BY avg_steals DESC
        ) ranked
    ) spg ON t.team_id = spg.team_id
    SET t.spg_rank = spg.rank;

    -- BPG Rank
    SET @bpg_rank = 0;
    UPDATE teams t
    JOIN (
        SELECT 
            team_id, 
            @bpg_rank := @bpg_rank + 1 AS rank
        FROM (
            SELECT 
                team_id, 
                total_blocks / games_played AS avg_blocks
            FROM teams 
            JOIN team_total_stats USING (team_id)
            ORDER BY avg_blocks DESC
        ) ranked
    ) bpg ON t.team_id = bpg.team_id
    SET t.bpg_rank = bpg.rank;
END //

DELIMITER ;

-- Call the procedure to update stats
CALL CalculateTeamStats();

CREATE TABLE Predictions (
    prediction_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL, -- Assumes you have a users table
    match_id INT(11) NOT NULL,
    predicted_team_id INT(11) NOT NULL,
    is_home_team BOOLEAN NOT NULL,
    predicted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (match_id) REFERENCES matches(match_id),
    FOREIGN KEY (predicted_team_id) REFERENCES teams(team_id)
);