<?php

function formatGameId($id)
{
  if (!is_numeric($id))
    return $id;
  return number_format($id, 0, ',', '-');
}

function formatAmount($amount)
{
  if (!is_numeric($amount))
    return $amount;
  return number_format($amount, 0, ' ', ' ');
}