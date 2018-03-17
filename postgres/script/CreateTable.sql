CREATE TABLE searches (
  id serial primary key,
  username varchar(100),
  numTweets int8,
  term varchar(100),
  location varchar(100),
  ignoreRetweets bool
);
