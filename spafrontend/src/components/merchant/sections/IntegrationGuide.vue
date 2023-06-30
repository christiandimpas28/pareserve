<script setup></script>
<template>
    <h2 class="text-title-md2 font-bold text-black white:text-dark">Getting Started with PaReserve API</h2>
    <p>PaReserve authenticates API calls with the use of authorization token.</p>
    <ul class="text-sm mt-4 space-y-6">
        <li>
            <p class="underline font-semibold">Authentication</p>
            <p>Get an access token based on username / password</p>
            <ol class="p-2 space-y-2 list-inside">
                <li>
                    <p class="font-semibold">API Login Endpoint:</p>
                    <p class="underline">{domain}/api/login</p>
                </li>
                <li>
                    <p class="font-semibold">Method:</p>
                    POST
                </li>
                <li>
                    <p class="font-semibold">Headers:</p>
                    <table class="table-fixed">
                        <thead>
                            <tr>
                                <th class="w-[40%]">Key</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Accept</td>
                                <td>application/vnd.api+json</td>
                            </tr>
                            <tr>
                                <td>Content-Type</td>
                                <td>application/vnd.api+json</td>
                            </tr>
                        </tbody>
                    </table>
                </li>
                <li>
                    <p class="font-semibold">Body:</p>
                    <table class="table-fixed">
                        <thead>
                            <tr>
                                <th class="w-[60%]">Key</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Email</td>
                                <td>{email}</td>
                            </tr>
                            <tr>
                                <td>Password</td>
                                <td>{password}</td>
                            </tr>
                        </tbody>
                    </table>
                </li>
                <li>
                    <p class="font-semibold">Reponse:</p>
                    Authorization Token (Plain Text)
                </li>
            </ol>
        </li>

        <li>
            <p class="underline font-semibold">Authorization Token</p>
            <p>Configure/Add Authorization Header on every API Calls</p>
            <ol class="p-2 space-y-2 list-inside">
                <li>
                    <p class="font-semibold">Authorization:</p>
                    <table class="table-fixed">
                        <tbody>
                            <tr>
                                <td class="w-[60%]">Type</td>
                                <td>Bearer Token</td>
                            </tr>
                            <tr>
                                <td>Token</td>
                                <td>{Authorization Token}</td>
                            </tr>
                        </tbody>
                    </table>
                </li>
            </ol>
        </li>

        <li>
            <p class="underline font-semibold">Available API Endpoints for Integration</p>
            <p></p>
            <ol class="p-2 space-y-2 space-x-1 list-inside">
                <li>
                    <table class="space-x-3">
                        <thead>
                            <tr>
                                <th class="min-w-fit">Title</th>
                                <th class="w-[15%]">Method</th>
                                <th>Url</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Merchant Profile</td>
                                <td>GET</td>
                                <td>{domain}/api/v2/partner/profile</td>
                            </tr>
                            <tr>
                                <td>Merchant Listings</td>
                                <td>GET</td>
                                <td>{domain}/api/v2/partner/listings</td>
                            </tr>
                            <tr>
                                <td>Merchant Transactions</td>
                                <td>GET</td>
                                <td>{domain}/api/v2/partner/bookings</td>
                            </tr>
                        </tbody>
                    </table>
                </li>
            </ol>
        </li>
    </ul>
</template>