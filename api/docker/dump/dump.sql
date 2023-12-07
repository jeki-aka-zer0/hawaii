insert into public.entity (entity_id, updated_at, name, description, created_at)
values  ('9fe96469-a845-416a-aa8f-d418e2469184', '2023-12-04 14:10:58', 'CGI', 'protocol for interaction between the web server and the application carried out via STDIN and STDOUT. A process is created and dies every time it is accessed.', '2023-12-04 14:10:58'),
        ('d8740a94-cc22-4caf-aa89-ad6bbb2f15b7', '2023-12-04 14:11:25', 'FastCGI', 'communication between client and server is carried out via Unix Sockets or via TCP/IP. Thanks to the demon, the process lives forever.', '2023-12-04 14:11:25'),
        ('2fb802c0-7baa-4fbd-a51d-5ffec986b95f', '2023-12-04 14:11:58', 'Event sourcing', 'an approach to handling operations on data that''s driven by a sequence of events, each of which is recorded in an append-only store.', '2023-12-04 14:11:58'),
        ('e8671c37-1281-466d-ba24-94c468e2c5d5', '2023-12-04 14:12:35', 'Flaky test', 'test that yields both passing and failing results despite zero changes to the code or test. In other words, flaky tests fail to produce the same outcome with each individual test run.', '2023-12-04 14:12:35'),
        ('6007c54c-12ad-4b08-ad8b-8b12405c6405', '2023-12-04 14:12:51', 'Graceful degradation', 'the property of a system where it continues to perform its primary function even if a significant part of the system has failed. Example: you can order a taxi when the chat with the driver has failed.', '2023-12-04 14:12:51');

insert into public.attribute (attribute_id, updated_at, name, created_at, type)
values  ('313eef01-1d08-4d7a-aa22-3809fa9bb854', '2023-12-04 14:10:59', 'Category', '2023-12-04 14:10:59', 0);

insert into public.value (value_id, entity_id, attribute_id, updated_at, created_at, value)
values  ('a9392469-b40a-48d5-9167-0b9e29831219', '9fe96469-a845-416a-aa8f-d418e2469184', '313eef01-1d08-4d7a-aa22-3809fa9bb854', '2023-12-04 14:10:59', '2023-12-04 14:10:59', 'Programming'),
        ('7a18ecaa-1c56-4ef8-ab69-7fec63047730', 'd8740a94-cc22-4caf-aa89-ad6bbb2f15b7', '313eef01-1d08-4d7a-aa22-3809fa9bb854', '2023-12-04 14:11:25', '2023-12-04 14:11:25', 'Programming'),
        ('e0c3958f-3c0d-4b51-8bb5-d7db65a220a9', '2fb802c0-7baa-4fbd-a51d-5ffec986b95f', '313eef01-1d08-4d7a-aa22-3809fa9bb854', '2023-12-04 14:11:58', '2023-12-04 14:11:58', 'Programming'),
        ('be09cf60-2468-4119-ad14-bdfe56ecdddb', 'e8671c37-1281-466d-ba24-94c468e2c5d5', '313eef01-1d08-4d7a-aa22-3809fa9bb854', '2023-12-04 14:12:35', '2023-12-04 14:12:35', 'Programming'),
        ('23eb53a4-1e32-4c11-bd6b-fe75d1411638', '6007c54c-12ad-4b08-ad8b-8b12405c6405', '313eef01-1d08-4d7a-aa22-3809fa9bb854', '2023-12-04 14:12:51', '2023-12-04 14:12:51', 'Programming');