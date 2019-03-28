#2019-01-11

ALTER TABLE `center_user`
  DROP INDEX uuid,
  ADD UNIQUE INDEX uuid (uuid);
