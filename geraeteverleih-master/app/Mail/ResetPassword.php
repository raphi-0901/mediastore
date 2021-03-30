<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Rennokki\LaravelMJML\LaravelMJML;
class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    public $token;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
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
            <mj-class name="bg-success" background-color="#42d29d"/>
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
                <mj-column mj-class="bg-primary" border-bottom="10px solid #7C8EeF" width="100%" padding="20px">
                    <mj-image width="30px"
                              src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+Cjxzdmcgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDM1OCAzNTgiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgeG1sbnM6c2VyaWY9Imh0dHA6Ly93d3cuc2VyaWYuY29tLyIgc3R5bGU9ImZpbGwtcnVsZTpldmVub2RkO2NsaXAtcnVsZTpldmVub2RkO3N0cm9rZS1saW5lam9pbjpyb3VuZDtzdHJva2UtbWl0ZXJsaW1pdDoyOyI+CiAgICA8ZyB0cmFuc2Zvcm09Im1hdHJpeCgxLDAsMCwxLC0xODM2LjI2LC03NjMuMTg1KSI+CiAgICAgICAgPGcgdHJhbnNmb3JtPSJtYXRyaXgoMSwwLDAsMSwxMTA4LjM3LDY4NS43OSkiPgogICAgICAgICAgICA8ZyB0cmFuc2Zvcm09Im1hdHJpeCgxLDAsMCwxLDY1MC44ODksLTU2OS41ODcpIj4KICAgICAgICAgICAgICAgIDxyZWN0IHg9IjE0NyIgeT0iOTY3LjA0MyIgd2lkdGg9IjIxOCIgaGVpZ2h0PSIzNy4xNDgiIHN0eWxlPSJmaWxsOnJnYigyMTYsMjE2LDIxNyk7Ii8+CiAgICAgICAgICAgIDwvZz4KICAgICAgICAgICAgPGcgdHJhbnNmb3JtPSJtYXRyaXgoMSwwLDAsMSw2OC4zNjQxLC02ODUuNzkpIj4KICAgICAgICAgICAgICAgIDxwYXRoIGQ9Ik03NTguNTQyLDc4Mi4wNzhDNzgyLjYwOCw3NjkuOTkxIDgwOS43NzgsNzYzLjE4NSA4MzguNTI1LDc2My4xODVDODY3LjI3Miw3NjMuMTg1IDg5NC40NDEsNzY5Ljk5MSA5MTguNTA3LDc4Mi4wNzhDOTI3LjM0Niw3NzguMDQxIDkzNy4xNzUsNzc1Ljc5IDk0Ny41MjUsNzc1Ljc5Qzk4Ni4xNTksNzc1Ljc5IDEwMTcuNTIsODA3LjE1NiAxMDE3LjUyLDg0NS43OUMxMDE3LjUyLDg3OC4wOTUgOTk1LjU5Myw5MDUuMzE5IDk2NS44MjEsOTEzLjM3TDk0Ny41MjUsMTA4My4yNUw3MjkuNTI1LDEwODMuMjVMNzExLjIyOSw5MTMuMzdDNjgxLjQ1Nyw5MDUuMzE5IDY1OS41MjUsODc4LjA5NSA2NTkuNTI1LDg0NS43OUM2NTkuNTI1LDgwNy4xNTYgNjkwLjg5MSw3NzUuNzkgNzI5LjUyNSw3NzUuNzlDNzM5Ljg3NSw3NzUuNzkgNzQ5LjcwMyw3NzguMDQxIDc1OC41NDIsNzgyLjA3OFoiIHN0eWxlPSJmaWxsOndoaXRlOyIvPgogICAgICAgICAgICA8L2c+CiAgICAgICAgICAgIDxnIHRyYW5zZm9ybT0ibWF0cml4KC0xLDAsMCwxLDE3NDUuNDgsLTY4NS43OSkiPgogICAgICAgICAgICAgICAgPHBhdGggZD0iTTg4Ni4xNjEsMTA2MC4zOEw4ODkuNTc1LDEwMDIuMDhDODg5Ljc5OSw5OTguMjU3IDg5My4wODcsOTk1LjMzMiA4OTYuOTEzLDk5NS41NTZDOTAwLjczOSw5OTUuNzggOTAzLjY2NCw5OTkuMDY5IDkwMy40NCwxMDAyLjg5TDkwMC4wMjYsMTA2MS4yQzg5OS44MDIsMTA2NS4wMiA4OTYuNTE0LDEwNjcuOTUgODkyLjY4OCwxMDY3LjcyQzg4OC44NjIsMTA2Ny41IDg4NS45MzcsMTA2NC4yMSA4ODYuMTYxLDEwNjAuMzhaIiBzdHlsZT0iZmlsbDpyZ2IoMjE2LDIxNiwyMTcpOyIvPgogICAgICAgICAgICA8L2c+CiAgICAgICAgICAgIDxnIHRyYW5zZm9ybT0ibWF0cml4KDEsMCwwLDEsNjguMzI5NSwtNjg1Ljc5KSI+CiAgICAgICAgICAgICAgICA8cGF0aCBkPSJNOTAwLjAyNiwxMDYxLjJMOTAzLjQ0LDEwMDIuODlDOTAzLjY2NCw5OTkuMDY5IDkwMC43MzksOTk1Ljc4IDg5Ni45MTMsOTk1LjU1NkM4OTMuMDg3LDk5NS4zMzIgODg5Ljc5OSw5OTguMjU3IDg4OS41NzUsMTAwMi4wOEw4ODYuMTYxLDEwNjAuMzhDODg1LjkzNywxMDY0LjIxIDg4OC44NjIsMTA2Ny41IDg5Mi42ODgsMTA2Ny43MkM4OTYuNTE0LDEwNjcuOTUgODk5LjgwMiwxMDY1LjAyIDkwMC4wMjYsMTA2MS4yWiIgc3R5bGU9ImZpbGw6cmdiKDIxNiwyMTYsMjE3KTsiLz4KICAgICAgICAgICAgPC9nPgogICAgICAgICAgICA8ZyB0cmFuc2Zvcm09Im1hdHJpeCgwLjY1MDAyMSwwLDAsMC42NTAwMjEsNDk2LjQxNSw2OS41NDU3KSI+CiAgICAgICAgICAgICAgICA8cGF0aCBkPSJNODU5Ljg0NywxMzkuMTU2Qzg1OS44NDcsMTYyLjY0NCA4NDYuNDcsMTgzLjAzMiA4MjYuOTMsMTkzLjExOUM4MjIuOTgzLDE5NS4xNTYgODE4Ljc4NCwxOTYuNzczIDgxNC4zOTMsMTk3LjkwOUM4MDkuODI2LDE5OS4wOTEgODA3LjA3OCwyMDMuNzU4IDgwOC4yNiwyMDguMzI1QzgwOS40NDEsMjEyLjg5MSA4MTQuMTA4LDIxNS42MzkgODE4LjY3NSwyMTQuNDU4QzgyNC4zMTMsMjEyLjk5OSA4MjkuNzAzLDIxMC45MjQgODM0Ljc3LDIwOC4zMDhDODU5LjgwOSwxOTUuMzgzIDg3Ni45NCwxNjkuMjUzIDg3Ni45NCwxMzkuMTU2Qzg3Ni45NCwxMzQuNDM5IDg3My4xMTEsMTMwLjYwOSA4NjguMzk0LDEzMC42MDlDODYzLjY3NiwxMzAuNjA5IDg1OS44NDcsMTM0LjQzOSA4NTkuODQ3LDEzOS4xNTZaIiBzdHlsZT0iZmlsbDpyZ2IoMjE2LDIxNiwyMTcpOyIvPgogICAgICAgICAgICA8L2c+CiAgICAgICAgICAgIDxnIHRyYW5zZm9ybT0ibWF0cml4KDEsMCwwLDEsNjguMzY0MSwtNjg1Ljc5KSI+CiAgICAgICAgICAgICAgICA8cGF0aCBkPSJNODQ1LjUwNCwxMDYwLjc5TDg0NS40NjksOTc1LjMyMUM4NDUuNDY4LDk3MS40ODkgODQyLjM1NSw5NjguMzc4IDgzOC41MjIsOTY4LjM4QzgzNC42ODksOTY4LjM4MSA4MzEuNTc5LDk3MS40OTQgODMxLjU4LDk3NS4zMjdMODMxLjYxNSwxMDYwLjc5QzgzMS42MTYsMTA2NC42MiA4MzQuNzI5LDEwNjcuNzQgODM4LjU2MiwxMDY3LjczQzg0Mi4zOTUsMTA2Ny43MyA4NDUuNTA1LDEwNjQuNjIgODQ1LjUwNCwxMDYwLjc5WiIgc3R5bGU9ImZpbGw6cmdiKDIxNiwyMTYsMjE3KTsiLz4KICAgICAgICAgICAgPC9nPgogICAgICAgICAgICA8ZyB0cmFuc2Zvcm09Im1hdHJpeCgwLjY1MDAyMSwwLDAsMC42NTAwMjEsMjc4LjQxNSw2OS41NDU3KSI+CiAgICAgICAgICAgICAgICA8cGF0aCBkPSJNNzQyLjQ3MiwxMTcuNDg1Qzc1MS4yMDUsOTQuNjgzIDc3My4zMDcsNzguNDc0IDc5OS4xNjUsNzguNDc0QzgwMy44ODIsNzguNDc0IDgwNy43MTIsNzQuNjQ0IDgwNy43MTIsNjkuOTI3QzgwNy43MTIsNjUuMjEgODAzLjg4Miw2MS4zODEgNzk5LjE2NSw2MS4zODFDNzY2LjAyNyw2MS4zODEgNzM3LjcwMSw4Mi4xNSA3MjYuNTA5LDExMS4zNzFDNzI0LjgyMiwxMTUuNzc2IDcyNy4wMjgsMTIwLjcyMiA3MzEuNDM0LDEyMi40MUM3MzUuODM5LDEyNC4wOTcgNzQwLjc4NSwxMjEuODkgNzQyLjQ3MiwxMTcuNDg1WiIgc3R5bGU9ImZpbGw6cmdiKDIxNiwyMTYsMjE3KTsiLz4KICAgICAgICAgICAgPC9nPgogICAgICAgICAgICA8ZyB0cmFuc2Zvcm09Im1hdHJpeCgxLDAsMCwxLDY4LjM2NDEsLTY4NS43OSkiPgogICAgICAgICAgICAgICAgPGcgdHJhbnNmb3JtPSJtYXRyaXgoMSwtMCwtMCwxLDY1OS41MjUsNzYzLjE4NSkiPgogICAgICAgICAgICAgICAgICAgIDx1c2UgeGxpbms6aHJlZj0iI19JbWFnZTEiIHg9IjE3OSIgeT0iMC4wMDEiIHdpZHRoPSIxNzlweCIgaGVpZ2h0PSIzNTcuMjA4cHgiIHRyYW5zZm9ybT0ibWF0cml4KDEsMCwwLDAuOTk3Nzg5LDAsMCkiLz4KICAgICAgICAgICAgICAgIDwvZz4KICAgICAgICAgICAgPC9nPgogICAgICAgIDwvZz4KICAgIDwvZz4KICAgIDxkZWZzPgogICAgICAgIDxpbWFnZSBpZD0iX0ltYWdlMSIgd2lkdGg9IjE3OXB4IiBoZWlnaHQ9IjM1OHB4IiB4bGluazpocmVmPSJkYXRhOmltYWdlL3BuZztiYXNlNjQsaVZCT1J3MEtHZ29BQUFBTlNVaEVVZ0FBQUxNQUFBRm1DQVlBQUFBbWtLdmJBQUFBQ1hCSVdYTUFBQTdFQUFBT3hBR1ZLdzRiQUFBU0wwbEVRVlI0bk8zZGFWUGpPaG9GNENPdjJST2c2YWJuenYvL1cxUHpZYmg5V1FLSnM5cXhOUitNSVVBQ3NhTXNmbldlcXE2bXVnT3huSVB5V3BJVmRYZDNyd0VOcFJRQUJhWHcralVBT0U3K3QxSUtqdVBBY1p4M1g2Ly9lZjBtb2hQd2lpKzAxZ0EwdEg3L2dEVGQ3UWRwRFRpT280dGd1NjRMejNQaHVtOS93TERUQVhuZlAyUTNTZ0ZhWjBqVERHa0tKTW5ueDdpdXB6OEczUE04cVB5dGdHZ3Z4c0s4aXpSZElVMVg3LzVOYThEelBPMzdIbnpmaCtkNThEd1BZQzlPSlIwMXpKc285UmJ5eFdMeCt1Kys3MnZQZXdzNHl4VDZ6c25EdkUyU0pFaVNCUFA1SEFEZ09BNkNJTkJCRU1EM0F6Z09TeE42NzJ6RC9GR1daVmdzRmxnc0ZxK2xTUmdHeU1QdEEreTFyYWVLb2JtNjgvMDgyR0VZd0hWZEJ0dEN0ZW1adjVNa01aSWt4blJhOU5vTk5Cb2huR0tnbk1RVDB6TnY0L3MrZ2lCa3NDMGdwbWZlcHJpUW5FNG44SDFmTTloeWlRL3p1aUxZazhrRVlSanFack9CSUFnQVhqeUtZRldZQzBvQmNieEVIQytobElObXM2R2J6ZWE1OWRZNnl6UzB6cUMxZnZjbnAxN1d5T1Ivci8rQnBiK2NWb1o1bmRZWlpyTVpwdFBaS1hwcm5hWXBWcXNWVnFzVjhxL1RUN09rWmEwdkd5Z21uRnpYRmI5c3dQb3dGOVo3YThkeDBHaVk3NjJ6TE5OSmttQzVYR0sxeWtOOGlIaHRXallBQUs3cjZud0kwNGZ2KytmMlRyUTM4YU1aKzJvMG1taTFtbFhIcm5XU0pJampHTXRsZkxEd1Z2RXk4WVFnOEY4bm51cmVjelBNTzlBYWFEUkN0Rm90ZUo3MzNRdXVpMm40T0k3WGF0enpwcFJDTVRaZjF4bFZocmtrM3cvUWJyZmcrLzY3Rnp2TE1sMU10NmU3TGdJL1V5OWxGaHFOUnExbVV4bm1pbnpmUjdQWmdsSjRXVE95UEpzU3dpVGY5OUZxdFJFRS90bTNqbUdtbmVTaGJwMzF1RHhITTJnblNaSmdOQnJCZFQyMDJ5MGRoaUZ3WnFGbW1LbVVORjFoUEI3RGRUMzBlbDI5d3dYeDBUaW5QZ0NxcHpSZFlUaDhRaFJOdEQ2VElSdUdtU3JMTDM3bmVId2NZckZZNUxmM254RERUSHZUT2tNVVJYaCtma2FhcGljTE5NTk14aVJKZ3VId3RaYytPb2Faakl1aUNGRVVIYjJXWnBqcElCYUxCWjZlamx0Mk1NeDBNUG1JeHhETDVmSW9nV2FZNmVERzR6Rm1zL25CQTgwdzAxRk1weE5NSnRPRER0OHh6SFEwOC9rTVVSUUJCd28wdzB4SHRWZ3NNQnFOY0lpUkRvYVpqaTZPNDRNRW1tR21rMGlTeEhqSndURFR5U3lYUzBUUkJEQVVhSWFaVG1xeG1HTTZuUnI1V1F3em5keHNOc044dnY4NE5NTk1aeUdLSm9qamVLOUFNOHgwRnBRQ3h1TUlXWlpWRGpURFRHZEQ2d3pqOFJpb2VFSElNTk5aeWJjZnJuWkJ5RERUMlpsT1o0ampwSFR2ekREVDJjbnI1M0hwK3BsaHByT2tkVmE2M0dDWTZXek41d3NreWU3bEJzTk1aMHNwbEpydVpwanByS1hwNnZWVGVyL0RNTlBabTA2bk8xME1Nc3gwOXJUV08xME1Nc3hVQy9QNUFtbjZkZS9NTUZNdEtKV3ZydnNLdzB5MXNWak12NnlkR1dhcWxhOUdOaGhtcXBYWmJJWXMyM3dqTE1OTXRUT2ZiNjZkR1dhcW5jVmlBV3lZRldTWXFYYXlMRU9TSkovK25XR21XbnJwbmQ5aG1LbVdsc3Y0MDQ1SUREUFZrdFlaNGpoKzkyOE1NOVhXeDFLRFlhYmFpdVAzcFFiRFRMVzJXcTFldjJhWXFkYlc2MmFHbVdwdGZieVpZYVphaStQa3RXNW1tS25XbEhycm5SbG1xajJHbWNSSWtueEVnMkdtMmt0VGhwbUVTTk1NV212Tk1GUHRLUVdrYWNxZW1XUmdtRWtNaHBuRVlKaEpqQ3pUOEU1OUVDUlhIQ2VZVEtaWUx2TjF4MkhZUUtmVFFSQ1lqNTNXR2RUZDNiMDIrUEhGUkFDQTUrY1JIaCtIK1BoWjcwb3BYRjFkWWpEb0czMCsxL1ZZWnBCNVVSVGg0ZUh4VTVDQmZFZlBoNGZINGtQZ2pXS1l5YWcwVGZIdzhQanQ0eDRlSHBHbXFiSG4xVHBqbU1tc3hXS0pOTTIrZlZ5YVpsZ3VsOGFlVjJ2MnpHUlltWUF1RnViQ25HWHNtY213OVh2eXZyTjUrOE5xbEdMUFRJWmwyZTUxc091YWk1OVNEc05NWnUxU0x4ZE1oaGxnejB5R2xRdXphK3g1SFVjeHpHUldtZUUycGN5RkdXQ1l5YkFzTzAyWndaNlpqSHJabVhQbng1c05NeThBeWFCTmV5WnY0empLYU0zc3VpN0RUT1lVcStOMjBXaUVScCtiUFRNWk5aL3ZQcU1YaGsyano4MmVtWXpaOWprajJ6U2JabnRtaHBtTTJlV0QydGVGb2Jrd2E4MHlnd3dhajNkZm4reDVudEdMUDgvem9KUlNERFB0YmJWS01aL3ZmdkZudXNRSXd3QUFwN1BKZ0xKM2piVGJiYVBQNy9zK0FJYVpEQ2dUWnNkUkRET2RwK2wwaGpqZWZSU2owK2xBS1dYcytYM2ZoM3I1Z1F3ejdlWHhjVmpxOGQxdTEranpGNzB5d0REVEhxSW8rdlRCa2wveFBBL05ac1BvTVFSQjhQbzF3MHlWYUszeCtQaFU2bnU2M1k3eDQyRFBUSHNiamNhbDd2Y0RnRjZ2Wi9RWVhvTDhXb0F6ekZSYUhDY1lEc3ZWeXIxZUY3NXZkbHV1OVJJRFlKaXBKSzAxL3Z6NUIxbTIrN3BscFJRdUx5K05IOHQ2aVFFd3pGVFN3OE93MUVVZkFQVDdQWGlleVZ1azhvVkZERE5WTnB2Tk1ScU5TbjJQNHloY1hBeU1IMHV6MlFUVzZtV0FZYVlkTFpjeC92bm5uOUxmTnhoY0dGMVVCT1I3WkRRYW40ZjRHR2I2Vmh3bnVMMjlMYldOQUpDWEFvT0IyUkVNQUdnMkc2K3pmdXNZWnZwU2txU1ZnZ3dBTnplLzREaG1JNmIxYTRueENjTk1XNjFXZVpCWHEvSmJ6MTVjREl6UDlnSDV2WU9PNDJ4YzNNR1BnYUNORm9zbC92NzdUNlU5bE1Nd3dPWGx4UUdPQ21pMVdsdi9qMkdtVDZJb3d0M2RRNms5TUFxT28zQnpjMk4wWlZ6QjkzMTRucmYxQnpQTTlNN0R3eU9lbjhzTnY2Mjd2djVoZkthdjBHeHU3NVVCaHBsZUxKY3g3dS92OTlvQWZERG9HMS9pV1hCZDkvWDJxRzBZWnN0bFdZYmg4QW1qMGJoU1dWSG85N3Y0OGVQSzRKRzk5MUlyZjFtN01Nd1dtMHdtdUwvZi80Tnl1dDBPcnErdkRSM1ZaNzRmYkp3aytZaGh0c3hxbFNLS0lvekhVYWxOVzdacHQxdjQrZk53UVFaZTcwNzU5b3FTWWJhQTFoclQ2UXpqOFJpejJkell6MjAyRzdpNStYV1FrWXRDcDlPQjYyNGVWLzZJWVJac3VVd3dIbzh4bVVTVlp2QyswdTEyOFBQbjlVR0Q3UHYrMXRtK1RSaG1nZWJ6T1liRHAxSWJzNVJ4ZFhXRml3dXpIeGU4eWE3bFJZRmhGdWJ4OFFsUFQrWHV6ZHVWNnpyNDllc1hXaTJ6TzNodTBtNjM0YnB1cVc2ZllSWWtpaVlIQzNJUStQajkrL2ZCSmtUV3VhNzM1YlQxTmd5eklFOVB6d2Y1dWYxK0QxZFhsOFpYd0czVDY1VXJMd29Nc3hCSnNpcDlPOU4zd2pEQTlmVzE4VjN1djlKcXRiNWNmL0VWaGxtSWZXYnZQbkpkQjVlWGwrajN6UytzLzBvWWhudnRROGN3QzJHcUJPajFPcmk2dWpKK3E5TjNmRDhvOXRXb1BOYkhNQXZoZVM0YWpRQ0xSZmxTdzNFVXV0MHVCb1BCVVM3d1BuSmRyM2dYMkd2UW1tRVc1UEx5RXJlM2YzWitmSDZQWGgvOWZ1OW9GM2Ziam1IVFBYMWxNY3lDdEZvdC9QejVBL2YzajF0cmFOZDEwRzYzMEc1MzBHbzFEenFEOXgybEhQVDcvYTIzUVpYRk1BdlQ2L1VRQkNIRzQvSExoK1lvT0k2RFZxdUpUcWRkYW5yNGtKUnlNQmowUzArTWZQa3o3Kzd1TldEdVNwaG9GM2w5N2h0OVcrRGQyWFJVV2dQOWZ0OTRrQUdXR1hSRVNpbjArejBFUVhDUVFwMWhwcU1vaHQ5TTFzZ2ZNY3gwY0dFWW90dnRHaGwrK3dyRFRBZWpkWDZueU11UzBZT1BBVExNZEJENUdISVBRV0QrUW04YmhwbU1PMFo5dkFuRFRFWWRxejdlaEdFbUl4ekhRYWZUUVJpR3dCSHE0MDBZWnRxTFVnNWFyZGJXRGNDUGlXR21Tb3BOdjl2dGxyR0ZRdnRpbUttMElBaGVObWM1N2dYZWR4aG0ycG5yZXVoMk93ZFpWMkVDdzB6ZnltLzlieGFiRjU1bGtBR0dtYmJJUDU0c1JLUFJxSHkzOUxFeHpQUk9HT1lCZnZsYzZscUV1TUF3VzA3ci9JS3UwUWdSaHVISmg5ZjJ3VEJick5sc290VTZuNkcxZmZGT0U0czFHZzB4UVFZWVpxdWRhbnVCUTVIVkd0cVoxZ3d6Q2VHNkRsQ3owWXJ2TU15V092WmVjc2ZBTUZ0S1dva0JNTXpXWXBoSkRNZGhtVUZDdkZ3QWlpS3ZSYlFUbGhra0Jzc01Fa0ZybGhra3hFdUpJV3JDQkdDWXJTU3hYZ1lZWml0SkxERUFodGxLRWkvK0FJYlpTdXlaU1F6V3pDU0d4QlZ6QU1Oc0pmYk1KQWJEVENJbzVkUjZPNEd2TU15V2tkb3JBd3l6ZFJobUVrUHFHRFBBTUZ1SFBUT0pJWFdNR1dDWXJjT2VtY1JnbUVrTWhwbkVZSmhKQkttM1N4VVlab3RJSHNrQUdHYXJTQzR4QUliWktsSnZseW93ekJaaHoweGlTRjZYQVRETVZtSFBUR0p3TklORWtQaUJQQi9KYmgyOWtqNWhBakRNMXBEZUt3TU1zeldrMThzQXcyd045c3draHZReFpvQmh0Z1o3WmhLRE5UT0p3WjZaeEdDWVNRVEorOHV0WTVndFlFT3ZERERNVm1DWVNRd2J4cGdCaHRrSzF2VE04L2tNV1phZCtqam9nR3dZWXdZQTcvYjJiMmdOQkVHQTYrc3JOSnZOVXg4VEdXWk56MXg4RWNjeC92ZS92M0YvLzNESzQ2RURzQzdNaGRGb2pLZW4wU21PaFE3RWxqSmo0Ni9zY0RoRUhDZkhQaFk2RUF2bVN3QnNDYlBXR2xFVUhmdFk2QUJlZW1VcjByeTFtRm91bDhjOERqb1FXK3BsZ0dFV2oyRkdYbXBRL2RseThRZHdCbEE4OXN3a0JzTk1ZckRNSURIWU01TUlOdXd2dDg2ZWxscm9aUjJ6RlJNbUFNTXNtazI5TXNBd2kyYlR4Ui9BTUl2R25wbkVrUDdwVWg4eHpJTFpjaU5yd2E3V1dvWmxCb25CTUpNWURET0pZTXYrY3VzWVpxRnM2NVVCaGxrc2hwbkVzRzFZRHJBb3pMWnRRV1pqeit5ZCtnQU9aYlZLTVJ3K1lqNWZZclZhUVd1TklBalFhalhSN3cvZys3Sm54MnhibHdFSURmUHo4d2pENFJCWjl2Nm0zRGlPRWNjeG9pakNyMTgvMFdxMVRuU0VoMmRqenl5dXhaUEpCQThQajUrQ3ZDNU5NOXplL3NGME9qdmlrUjBYdzF4elNiTEMzZDM5em8rL3YzOFF1NldDaldXR3FEQkgwZVRMSHZtajFXcUYwV2g4d0NNNkhkc21UQUJoWVo3UHk1Y05FbmR1c3JGWEJvU0ZlYkVvSDh3a2lROXdKS2RsWTcwTUNBcHpsbVdWNnQ4MGxUZit6RERYWEpsYWVaM0VGNTVsUnMxVm5lR1RHR2FKYmRxRm1GWlhMUmNrdnZBUzI3UUxNYTNXT3EzMGZSSmZlSllaTmNjeTQ0M0VOdTFDVEt0WlpyeVIyS1pkaUdsMTlkRU1XVy9KTDBHMmJ2WVBFQlhtYWpXejY4cDYzVzJ0bHdGUllXYVpBY2hyVHhsaVdzNHc1NlMxcHd3eExhOGFacVZrdlMxTHV3WW9RMUNZcTliTVlrNEJBSG50S1VOTXk5T1Vhek1BZWUwcFEwekxxOWZNc2tZekdHWUJ0T1lGSU1DaHVZM3FkbXRjbFVrVGFmV2xVdlpPbUFCQ2V1WXMwNVVXNXIrOCtHSkllNWNwUzBUck9jYWNrL1pPVTVhSTF2UGlMMmZ6R0ROZ2VaaWw5V1RTMmxPV2lOWlg3NWxsOVdUU3lxYXlSTFMrNnV5ZnRCZGZXbnZLRXRGNlhnRG1iQjVqQm9TRW1WUFpPV250S1V0RTY5a3pBMG9wSy9lWFd5ZmkxZVNLT1hrWHMxV0llRFc1TGtQZW1Ia1ZJbDdOcW5kbVM1ck90djNpRHhBU1pvNHp5M3FYcVVyRUdlQU1JTU1NQ0FsejlRMWc1TlNaa3Q1bHFoSVJabDRBeW5xWHFVckVHYWl5TUY5U3J3ekkrc1dzcXZablFPdHFDL09sdlMwenpBTEN6QTBUN2Q1ZmJsM3RYMUV1ek9jWWM4SGlNTmUrNmE4a3RXVWZ0VDhMSEdObW1BdTFQd3VjL1pQVmxuMElDSE8xRlhPeTFtWElhY3MrYW44V3F1NllMeWtBTEROeXRUOExuTXBtbUF1MVB3dTIxOHhhYzJpdVVQc3dWLzM4UHlsbEJpZE0zdFQrRmEyK1kzN3RtdzZBSmNhNjJwOEoyNmV6V1dLOHFmMHJhbnZOTE9XWDBvVGFuNG5xUTNNeXlrd3B0YjhKdFQ4VHRtL05KYVVkSnRUK1ROaStNSjgxODV0YWg3bnF3bndwSXhrQWUrWjF0VDRUMVZmTXllbk5HT1kzdFQ0VHRrOWxjMys1OTJvZFp0c1g1a3NaWGpTbDFxOXExUzBHcEF4blNmbWxOS1hXWjRPemZ6TGFZVXF0ejBiMWRSa3kzcDZsL0ZLYXN2VnMxT0VpcVdyUEpLVkhZODM4M3RaWE5RaUNZeDVISlo1WDdSZ2JqWWJoSXprTktiK1VwbXc5RzJGNC9pOTRFSGlsMzBFY1I2SFJDQTkwUk1mRk11TzlqV2ZEY1JUNi9kNnhqNlUwcFJRR2c0dFMzOVBwZENCbGFKWmx4bnNidzN4MWRRWGY5NDU5TEpWY1hQVGgrLzVPai9WOUh6OStYQjM0aUk2bkR0YzF4L1F1ekVvcFhGNWUxcUpYTGlpbDhOZGYvL3EyZEFnQ0g3OS8zNGg1YStidFVwOTVRSDRoRVlZaHJxNnVFSWJuZitIM2tlZTUrUGUvLzhMVDB6T20wd2tXaTNqdC96d01CbjMwK3oweDVRVWdhMzJKS2VyMjlsWjdYajFLaWwxbFdZYlZLb1h2ZTZJQ3ZDNE1RL1I2UFptTnE4anpQQjlBdGJzMXpwWGpPQWdDR2VYRU51eVpQNVA5aWdzbWFVMjJLVHdqTmNVSms4OTRSbXBLeXFpTVNUd2pOY1dhK1RPR3VZYTBacys4Q2M5SURYSENaRE9HdVliWUsyL0dzMUpEckpjM1k1aHJpRDN6Wm82MDJUOGJjSXg1TTU2VkdtTFB2Qm5QU2cxeFVmNW1ESE1Oc2N6WXpFdlRhbHZDMHVtd3pOak0rODkvL252cVk2Q1NibTV1T0dHeUFYL0ZTUXlHbWNSZ21Fa01ocG5FWUpoSkRJYVp4R0NZU1F5R21jUmdtRWtNaHBuRVlKaEpESWFaeEdDWVNReUdtY1JnbUVrTWhwbkVZSmhKRElhWnhHQ1lTUXlHbWNSZ21Fa01ocG5FWUpoSkRJYVp4R0NZU1F5R21jUmdtRWtNaHBuRVlKaEpESWFaeEdDWVNReUdtY1JnbUVrTWhwbkVZSmhKRElhWnhQZy9ZYmRuTzBZSDBpMEFBQUFBU1VWT1JLNUNZSUk9Ii8+CiAgICA8L2RlZnM+Cjwvc3ZnPgo="
                              href="' . env("APP_URL") . '"></mj-image>
                </mj-column>
            </mj-section>
            <mj-section mj-class="text-container">
                <mj-column>
                    <mj-text mj-class="h1" align="center">Hallo ' . $this->user->firstName . '!</mj-text>

                    <mj-divider border-color="#eef2f7"></mj-divider>
                    <mj-text align="center">Das passiert jedem einmal!<br>Bitte klicken Sie den Button, um Ihr Passwort zurückzusetzen.
                    </mj-text>
                    <mj-button mj-class="bg-primary" href="'. route('password.reset', $this->token) .  '">Passwort zurücksetzen</mj-button>
                                        <mj-text align="center">Falls Sie diese E-Mail nicht angefordert haben, ignorieren Sie sie einfach.</mj-text>
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
            ->subject("Passwort zurücksetzen");
    }
}
