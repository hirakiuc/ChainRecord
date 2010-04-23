
CREATE TABLE groups(
  id INTEGER NOT NULL AUTO_INCREMENT,
  title TEXT,
  memo TEXT,
  PRIMARY KEY (id)
); 
--CREATE UNIQUE INDEX group_uidx ON groups(id);


CREATE TABLE albums(
  id INTEGER NOT NULL AUTO_INCREMENT,
  group_id INTEGER NOT NULL,
  title TEXT,
  memo TEXT,
  FOREIGN KEY(group_id) REFERENCES groups(id),
  PRIMARY KEY (id)
); 
--CREATE UNIQUE INDEX album_uidx ON albums(id);


CREATE TABLE pictures(
  id INTEGER NOT NULL AUTO_INCREMENT,
  album_id INTEGER NOT NULL,
  path TEXT NOT NULL,
  title TEXT,
  memo TEXT,
  FOREIGN KEY(album_id) REFERENCES albums(id),
  PRIMARY KEY (id)
);
--CREATE UNIQUE INDEX picture_uidx ON pictures(id);

