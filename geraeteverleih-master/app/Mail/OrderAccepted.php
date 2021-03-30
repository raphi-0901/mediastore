<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Rennokki\LaravelMJML\LaravelMJML;

class OrderAccepted extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $api = (new LaravelMJML())->setAppId(env("MJML_APP_ID", ''))->setSecretKey(env('MJML_SECRET_KEY', ''));

        $mjml = '<mjml>
    <mj-head>
        <mj-attributes>
            <mj-class name="h1" font-size="22px" font-weight="bold" padding="20px"/>
            <mj-class name="h2" font-size="20px" />
            <mj-class name="bg-primary" background-color="#8C9EFF"/>
            <mj-class name="bg-info" background-color="#39afd1"/>
            <mj-class name="bg-success" background-color="#40b842 "/>
            <mj-class name="bg-warning" background-color="#ffbc00"/>
            <mj-class name="bg-danger" background-color="#fa5c7c"/>
            <mj-class name="bg-dark" background-color="#313a46"/>
            <mj-class name="bg-light" background-color="#eef2f7"/>
            <mj-class name="text-dark" color="#313a46"/>
            <mj-class name="text-grey" color="#838f9c"/>
            <mj-class name="text-primary" color="#8C9EFF"/>
            <mj-class name="text-info" color="#39afd1"/>
            <mj-class name="text-success" color="#42d29d"/>
            <mj-class name="text-warning" color="#ffbc00"/>
            <mj-class name="text-danger" color="#fa5c7c"/>
            <mj-class name="text-light" color="#eef2f7"/>
            <mj-class name="text-container" padding-left="15px" padding-right="15px"/>
            <mj-all font-family="Helvetica Neue, Helvetica, Arial, sans-serif"></mj-all>
            <mj-text font-weight="400" font-size="16px" color="#313a46" line-height="24px" font-family="Helvetica Neue, Helvetica, Arial, sans-serif"></mj-text>
            <mj-table font-size="14px"/>
            <mj-font name="FontAwesome" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"/>
        </mj-attributes>
        <mj-style>
            .box-shadow {
            box-shadow: 0px 0px 32px 0px rgba(0,0,0,0.25);
            }
        </mj-style>
        <mj-style inline="inline">
            .body-section {
            -webkit-box-shadow: 0px 5px 50px 0px rgba(0,0,0,0.20);
            -moz-box-shadow: 0px 5px 50px 0px rgba(0,0,0,0.20);
            box-shadow: 0px 5px 50px 0px rgba(0,0,0,0.20);
            margin-top: 30px!important;
            margin-bottom: 30px!important;
            }
        </mj-style>
        <mj-style inline="inline">
            ul {
            display: inline-block;
            text-align:left;
            margin:0;
            padding:0;
            }
        </mj-style>
    </mj-head>
    <mj-body background-color="#fafbfe" width="600px">
        <mj-wrapper background-color="#fff" padding="0" css-class="body-section">
            <mj-section full-width="full-width" padding="0">
                <mj-column mj-class="bg-success" border-bottom="10px solid #38ab3a" width="100%" padding="20px">
                    <mj-image width="30px"
                              src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyMy4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9IjAgMCA1MTUgNDk0IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1MTUgNDk0OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQo8c3R5bGUgdHlwZT0idGV4dC9jc3MiPg0KCS5zdDB7ZmlsbDojRkZGRkZGO30NCjwvc3R5bGU+DQo8Zz4NCgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMTgxLjIsNTIuMmMyLjgtMS42LDUuOCwwLjgsNi45LDMuNGMyLjYsNS44LDIuOCwxMi4yLDMuMiwxOC40YzEsMzQuNy0xLjYsNjkuMy0xLDEwMy45DQoJCWMwLjYsMTguMSwxLjUsMzYuNCw2LjksNTMuOGMzLjIsOS42LDguMywxOS40LDE3LjQsMjQuN2M4LjksNS4yLDE5LjgsNC45LDI5LjYsMi42YzIxLjgtNC45LDQyLjctMTMsNjMuNS0yMS4zDQoJCWMxNi4yLTYuNiwzMi4yLTEzLjcsNDcuOS0yMS4yYzE2LTcuNywzMS42LTE2LjIsNDguNi0yMS40YzUuNC0xLjUsMTEuMS0zLjEsMTYuNy0yLjJjMi4zLDAuMyw0LjMsMi43LDMuNyw1LjENCgkJYy0wLjUsMi45LTIuNSw1LjItNC40LDcuM2MtNy40LDcuOC0xNi40LDEzLjctMjUuMiwxOS44Yy0zNS41LDI0LjUtNzAuNiw0OS41LTEwNi41LDczLjRjLTE0LjksOS43LTI5LjksMTkuNC00NS45LDI3LjENCgkJYy0xOC44LDguNS0zOS43LDE0LjMtNjAuNCwxMS42Yy0xMy0xLjYtMjUuNi04LjgtMzIuMS0yMC40Yy04LjktMTUuMy05LjEtMzMuOS03LjUtNTFjNi4xLTUwLDEzLjctOTkuNywyMS44LTE0OS40DQoJCWMyLjItMTMuNSw0LjUtMjcsNi40LTQwLjVjMC45LTYuMiwyLjQtMTIuNSw1LjItMTguMUMxNzcuMyw1NS43LDE3OC44LDUzLjQsMTgxLjIsNTIuMnoiLz4NCgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMCw1M2MyLjcsMCw1LjMsMCw4LDBjMCwxMS40LDAsMjIuOCwwLDM0LjNjMTAuNywwLDIxLjQsMCwzMi4xLDBjMi4yLTAuMyw0LjMsMS40LDQuMiwzLjdjMC4xLDEzLDAsMjYsMCwzOS4xDQoJCWMtMi43LDAtNS4zLDAtOCwwYzAtMTEuNiwwLTIzLjIsMC0zNC44Yy0xMC40LDAtMjAuOSwwLjEtMzEuMywwYy0yLjEsMC4yLTMuOS0wLjktNS0yLjdWNTN6Ii8+DQoJPHBhdGggY2xhc3M9InN0MCIgZD0iTTU1LjgsNTNjMi43LDAsNS4zLDAsOCwwYzAsMTEuNCwwLDIyLjgsMCwzNC4yYzEuOSwwLDMuOCwwLDUuOCwwYzAsMi43LDAsNS4zLDAsOGMtMS45LDAtMy44LDAtNS43LDANCgkJYzAsMTEuNiwwLDIzLjIsMCwzNC43Yy0yLjcsMC01LjMsMC04LDBjMC0xMS42LDAtMjMuMiwwLTM0LjhjLTEuOSwwLTMuOCwwLTUuOCwwYzAtMi43LDAtNS4zLDAtOGMxLjksMCwzLjgsMCw1LjcsMA0KCQlDNTUuOCw3NS44LDU1LjgsNjQuNCw1NS44LDUzeiIvPg0KCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik03NC44LDUzYzIuNywwLDUuMywwLDgsMGMwLDI1LjcsMCw1MS4zLDAsNzdjLTIuNywwLTUuMywwLTgsMEM3NC44LDEwNC4zLDc0LjgsNzguNyw3NC44LDUzeiIvPg0KCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik00MDQuMywyNzEuNWM1LjEtMiwxMS40LDAuMiwxNC4zLDQuOWMzLjUsNS42LDMuOCwxMi40LDMuMSwxOC44Yy0yLjcsMjUuOC02LjEsNTEuNC05LjYsNzcuMQ0KCQljLTMuMywyMy42LTYuNiw0Ny4zLTEwLjEsNzAuOWMtMSw1LTIsMTAuNS01LjYsMTQuNGMtMS43LDEuOS01LjMsMi4yLTYuNy0wLjJjLTIuMi0zLjMtMi40LTcuNS0yLjQtMTEuM2MxLjMtMjQuNiwyLjItNDkuMiwzLTczLjgNCgkJYzEtMjcuMSwxLjYtNTQuMiwyLTgxLjNDMzkyLjMsMjgzLDM5Ni41LDI3NC4zLDQwNC4zLDI3MS41eiIvPg0KPC9nPg0KPC9zdmc+DQo="
                              href="' . env("APP_URL") . '"></mj-image>
                </mj-column>
            </mj-section>
            <mj-section mj-class="text-container">
                <mj-column>
                    <mj-text mj-class="h1" align="center">Bestellung akzeptiert</mj-text>
                    <mj-divider border-color="#eef2f7"></mj-divider>
                    <mj-text align="center">Die Bestellung vom '. $this->order->from->isoFormat("Do MMMM") .' bis zum ' . $this->order->to->isoFormat("Do MMMM") . ' wurde von '.$this->order->answeredBy->displayName().' akzeptiert.</mj-text>
                     <mj-button mj-class="bg-success" href="'. route('orders.show', $this->order->id) .  '">Ansehen</mj-button>
                    <mj-divider border-color="#eef2f7" padding="20px"></mj-divider>
            </mj-column>
            </mj-section>
            <mj-section mj-class="bg-light">
                <mj-column>
                    <mj-text align="center" font-size="13px" mj-class="text-grey">' . env("APP_NAME") . ' | ' . date("Y") . '</mj-text>
                </mj-column>
            </mj-section>
        </mj-wrapper>
    </mj-body>
</mjml>';

        $html = $api->render($mjml);
        return $this->html($html)
            ->subject("Bestellung wurde akzeptiert ");
    }
}
