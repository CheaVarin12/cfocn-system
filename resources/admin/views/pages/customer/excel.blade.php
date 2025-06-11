<table>
    <tr>
        <td style="vertical-align:middle; text-align:center" colspan="10">Operator Name : CFOCN</td>
    </tr>
    <tr>
        <th style="vertical-align:middle; text-align:center">No</th>
        <th style="vertical-align:middle; text-align:center">Customer ID</th>
        <th style="vertical-align:middle; text-align:center">Register Date</th>
        <th style="vertical-align:middle; text-align:center">Name EN</th>
        <th style="vertical-align:middle; text-align:center">Name KH</th>
        <th style="vertical-align:middle; text-align:center">Phone</th>
        <th style="vertical-align:middle; text-align:center">Email</th>
        <th style="vertical-align:middle; text-align:center">VAT</th>
        <th style="vertical-align:middle; text-align:center">Address EN</th>
        <th style="vertical-align:middle; text-align:center">Address KH </th>
    </tr>
    @foreach ($customers as $k => $customer)
    <tr>
        <td style="vertical-align:middle; text-align:center">{{ $k+1 }}</td>
        <td style="vertical-align:middle; text-align:left">{{ $customer->customer_code }}</td>
        <td style="vertical-align:middle; text-align:left">{{ $customer->register_date }}</td>
        <td style="vertical-align:middle; text-align:left">{{ $customer->name_en }}</td>
        <td style="vertical-align:middle; text-align:left">{{ $customer->name_kh }}</td>
        <td style="vertical-align:middle; text-align:left">{{ $customer->phone }}</td>
        <td style="vertical-align:middle; text-align:left">{{ $customer->email }}</td>
        <td style="vertical-align:middle; text-align:left">{{ $customer->vat_tin }}</td>
        <td style="vertical-align:middle; text-align:left">{{ $customer->address_en }}</td>
        <td style="vertical-align:middle; text-align:left">{{ $customer->address_kh }}</td>
    </tr>
    @endforeach
</table>
