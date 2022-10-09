INSERT INTO hospital (
  year,
  average,
  median,
  category_name,
  category_id
) VALUES (
  2002,
  14.1,
  42.4,
  'hospital per 100km2',
  '#I0950102'
);

INSERT INTO hospital (
  year,
  average,
  median,
  category_name,
  category_id
) VALUES (
  2000,
  15.5,
  55.4,
  'clinic per 100km2',
  '#I0950103'
);

DELETE FROM hospital WHERE year = 2002;
