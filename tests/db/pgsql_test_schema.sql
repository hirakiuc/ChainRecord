
CREATE TABLE groups(
  id SERIAL NOT NULL,
  title TEXT,
  memo TEXT
); 
CREATE UNIQUE INDEX group_uidx ON groups(id);


CREATE TABLE albums(
  id SERIAL NOT NULL,
  group_id INTEGER NOT NULL,
  title TEXT,
  memo TEXT,
  FOREIGN KEY(group_id) REFERENCES groups(id)
); 
CREATE UNIQUE INDEX album_uidx ON albums(id);


CREATE TABLE pictures(
  id SERIAL NOT NULL,
  album_id INTEGER NOT NULL,
  path TEXT NOT NULL,
  title TEXT,
  memo TEXT,
  FOREIGN KEY(album_id) REFERENCES albums(id)
);
CREATE UNIQUE INDEX picture_uidx ON pictures(id);

