CREATE TABLE task3 (
  id INT(11) NOT NULL AUTO_INCREMENT,
  receiving_date TIMESTAMP NOT NULL,
  tracking_number VARCHAR(200),
  product_name VARCHAR(150),
  cbm DECIMAL(10,0),
  weight DECIMAL(10,0),
  PRIMARY KEY (id)
);