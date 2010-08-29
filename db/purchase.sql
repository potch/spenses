-- *************************** 1. row ***************************
--      purchaseid: 1
--     description: test
-- purchase_amount: 10.00
--    date_updated: 0000-00-00 00:00:00
--    date_created: 2010-07-28 18:44:52
--         date_of: 2010-07-28 00:00:00
--       is_settle: 0
--  creator_userid: 1
--    creator_nick: Andrew
--    payer_userid: 1
--      payer_nick: Andrew
--    payee_userid: 2
--      payee_nick: Potch
--      iou_amount: 4.00
-- *************************** 2. row ***************************
--      purchaseid: 1
--     description: test
-- purchase_amount: 10.00
--    date_updated: 0000-00-00 00:00:00
--    date_created: 2010-07-28 18:44:52
--         date_of: 2010-07-28 00:00:00
--       is_settle: 0
--  creator_userid: 1
--    creator_nick: Andrew
--    payer_userid: 1
--      payer_nick: Andrew
--    payee_userid: 3
--      payee_nick: Nick
--      iou_amount: 6.00

SELECT
 purchase.purchaseid,
 purchase.description,
 purchase.amount AS purchase_amount,
 purchase.date_updated AS date_updated,
 purchase.date_created AS date_created,
 purchase.date_of AS date_of,
 is_settle,
 purchase.userid AS creator_userid,
 creator.nick AS creator_nick,
 purchase.userid_payer AS payer_userid,
 payer.nick AS payer_nick,
 payee.userid AS payee_userid,
 payee.nick AS payee_nick,
 iou.amount AS iou_amount
FROM purchase
LEFT JOIN user AS creator ON creator.userid=purchase.userid
LEFT JOIN user AS payer ON payer.userid=userid_payer
LEFT JOIN iou USING(purchaseid)
LEFT JOIN user AS payee ON payee.userid=userid_payee
WHERE purchaseid IN (1) \G