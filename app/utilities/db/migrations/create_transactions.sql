-- Run this once on your SQL Server database
CREATE TABLE transactions (
    id          INT IDENTITY(1,1) PRIMARY KEY,
    player_id   INT            NOT NULL,
    type        VARCHAR(20)    NOT NULL,   -- 'bet', 'win', 'deposit', 'withdrawal'
    amount      DECIMAL(18,2)  NOT NULL,
    balance_before DECIMAL(18,2) NOT NULL,
    balance_after  DECIMAL(18,2) NOT NULL,
    game        INT            NULL,
    round_id    INT            NULL,
    reference   VARCHAR(255)   NULL,
    created_at  DATETIME       DEFAULT GETDATE()
);
