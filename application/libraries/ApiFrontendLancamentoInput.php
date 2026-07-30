<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ApiFrontendLancamentoInput
{
    public function validate($input)
    {
        $fields = array();
        $input = is_array($input) ? $input : array();

        $description = trim((string) ($input['description'] ?? ''));
        if ($description === '') {
            $fields['description'] = 'Informe a descrição.';
        }

        $amount = $this->normalizeAmount($input['amount'] ?? null);
        if ($amount === null || (float) $amount <= 0) {
            $fields['amount'] = 'Informe um valor decimal maior que zero.';
        }

        $transactionDate = $this->normalizeDate($input['transactionDate'] ?? null);
        if ($transactionDate === null) {
            $fields['transactionDate'] = 'Informe uma data válida no formato YYYY-MM-DD.';
        }

        $paymentDateValue = array_key_exists('paymentDate', $input)
            ? $input['paymentDate']
            : $transactionDate;

        if ($paymentDateValue === null || $paymentDateValue === '') {
            $paymentDate = $transactionDate;
        } else {
            $paymentDate = $this->normalizeDate($paymentDateValue);
            if ($paymentDate === null) {
                $fields['paymentDate'] = 'Informe uma data válida no formato YYYY-MM-DD.';
            }
        }

        $type = filter_var($input['type'] ?? null, FILTER_VALIDATE_INT);
        if (!in_array($type, array(1, 2), true)) {
            $fields['type'] = 'Use 1 para entrada ou 2 para saída.';
        }

        $paymentMethodId = filter_var($input['paymentMethodId'] ?? null, FILTER_VALIDATE_INT);
        if ($paymentMethodId === false || $paymentMethodId < 1) {
            $fields['paymentMethodId'] = 'Informe uma forma de pagamento válida.';
        }

        foreach (array('paid', 'hidden') as $booleanField) {
            if (array_key_exists($booleanField, $input) && !is_bool($input[$booleanField])) {
                $fields[$booleanField] = 'Use true ou false.';
            }
        }

        if ($fields !== array()) {
            return array(
                'valid'  => false,
                'fields' => $fields,
                'data'   => null,
            );
        }

        return array(
            'valid'  => true,
            'fields' => array(),
            'data'   => array(
                'description'     => $description,
                'amount'          => $amount,
                'transactionDate' => $transactionDate,
                'paymentDate'     => $paymentDate,
                'paid'            => !empty($input['paid']),
                'provider'        => $this->nullableText($input['provider'] ?? null),
                'paymentMethodId' => (int) $paymentMethodId,
                'type'            => (int) $type,
                'notes'           => $this->nullableText($input['notes'] ?? null),
                'hidden'          => !empty($input['hidden']),
            ),
        );
    }

    private function normalizeAmount($amount)
    {
        $amount = trim((string) $amount);

        if (!preg_match('/^\d+(?:\.\d{1,2})?$/', $amount)) {
            return null;
        }

        return number_format((float) $amount, 2, '.', '');
    }

    private function normalizeDate($date)
    {
        $date = trim((string) $date);
        $parsed = DateTime::createFromFormat('!Y-m-d', $date);

        return $parsed && $parsed->format('Y-m-d') === $date ? $date : null;
    }

    private function nullableText($value)
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
