<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Chiiya\Passes\Google\JWT;
use Chiiya\Passes\Apple\PassFactory;
use Illuminate\Http\RedirectResponse;
use Chiiya\Passes\Apple\Components\Field;

use Chiiya\Passes\Apple\Components\Image;
use Chiiya\Passes\Apple\Passes\EventTicket;
use Chiiya\Passes\Google\Enumerators\State;
use Chiiya\Passes\Google\ServiceCredentials;
use Chiiya\Passes\Apple\Enumerators\ImageType;
use Chiiya\Passes\Google\Enumerators\BarcodeType;
use Chiiya\Passes\Apple\Components\SecondaryField;
use Chiiya\Passes\Google\Passes\EventTicketObject;
use Chiiya\Passes\Google\Components\Common\Barcode;
use Chiiya\Passes\Google\Components\Common\DateTime;
use Chiiya\Passes\Google\Components\Common\GroupingInfo;
use Chiiya\Passes\Google\Components\Common\TimeInterval;
use Chiiya\Passes\Google\Components\Common\TextModuleData;
use Chiiya\Passes\Google\Components\Generic\Notifications;
use Chiiya\Passes\Google\Components\Common\LocalizedString;
use Chiiya\Passes\Google\Enumerators\BarcodeRenderEncoding;
use Chiiya\Passes\Google\Components\Common\Image as GoogleImage;
use Chiiya\Passes\Google\Components\Generic\UpcomingNotification;

class PassController extends Controller
{
    public function apple(Attendee $attendee, Request $request)
    {
        if ($request->user()->id !== $attendee->user_id) {
            abort(403, 'You don\'t have access to this pass');
        }

        $edition = $attendee->edition;
        $pass = new EventTicket(
            description: $edition->name . ' Ticket',
            organizationName: 'European Greens',
            passTypeIdentifier: 'pass.eu.europeangreens.congress',
            serialNumber: $edition->id  .'-' . $attendee->id,
            teamIdentifier: 'P9EYEPC9S9',
            backgroundColor: 'rgb(15, 138, 84)',
            foregroundColor: 'rgb(255, 255, 255)',
            labelColor: 'rgb(255, 220, 46)',
            barcode: [
                "message" => $attendee->qr_code . " APPLEPASS",
                "format" => "PKBarcodeFormatQR",
                "messageEncoding" => "iso-8859-1"
            ],
            expirationDate: '2024-12-09T00:00:00+01:00',
            relevantDate: '2024-12-06T20:00:00Z',
            headerFields: [
                new SecondaryField(key: 'dates', value: "39th Congress", label: '6-8 Dec 2024'),
            ],
            primaryFields: [
                new Field(key: 'name', value: $attendee->user->fullName(), label: 'Name')
            ],
            secondaryFields: [
                new SecondaryField(key: 'name', value: $attendee->user->group->name, label: 'Organisation'),
                new SecondaryField(key: 'role', value: $attendee->type->name, label: 'Role'),
            ]
        );
        
        $pass
            ->addImage(new Image(resource_path('images/pass/egp.png'), ImageType::LOGO))
            ->addImage(new Image(resource_path('images/pass/egp@2x.png'), ImageType::LOGO, 2))
            ->addImage(new Image(resource_path('images/pass/egp@3x.png'), ImageType::LOGO, 3))
            ->addImage(new Image(resource_path('images/pass/icon.png'), ImageType::ICON))
            ->addImage(new Image(resource_path('images/pass/icon@2x.png'), ImageType::ICON, 2))
            ->addImage(new Image(resource_path('images/pass/icon@3x.png'), ImageType::ICON, 3));
        
        $factory = new PassFactory();
        $factory->setCertificate(config('passes.apple.certificate'));
        $factory->setPassword(config('passes.apple.password'));
        $factory->setWwdr(config('passes.apple.wwdr'));
        $factory->setOutput(storage_path('passes'));
        $file = $factory->create($pass, 'pass-' . $edition->id . '-' . $attendee->id);

        return response()->download($file);
    }

    public function google(Attendee $attendee, Request $request): RedirectResponse
    {
        if ($request->user()->id !== $attendee->user_id) {
            abort(403, 'You don\'t have access to this pass');
        }

        $credentials = ServiceCredentials::parse(config('passes.google.credentials'));
        $object = new EventTicketObject(
            classId: '3388000000022779916.39thCongressTicket',
            id: '3388000000022779916.' . $attendee->edition->id . $attendee->id,
            logo: GoogleImage::make('https://congress.europeangreens.eu/build/assets/egp-DHMXdr_S.svg'),
            heroImage: GoogleImage::make($attendee->edition->cover),
            hexBackgroundColor: '#0F8A54',
            state: State::ACTIVE,
            barcode: new Barcode(
                type: BarcodeType::QR_CODE,
                renderEncoding: BarcodeRenderEncoding::UTF_8,
                value: $attendee->qr_code . " GOOGLEPASS",
            ),
            validTimeInterval: new TimeInterval(
                start: new DateTime(date: '2024-12-06T11:00:00'),
                end: new DateTime(date: '2024-12-08T13:00:00')
            ),  
            notifications: new Notifications(
                upcomingNotification: new UpcomingNotification(
                    enableNotification: true
                ),
            ),
            textModulesData: [
                new TextModuleData(
                    id: 'fullName',
                    header: 'Name',
                    body: $attendee->user->fullName(),
                ),
                new TextModuleData(
                    id: 'organisation',
                    header: 'Organisation',
                    body: $attendee->user->group->name,
                ),
                new TextModuleData(
                    id: 'role',
                    header: 'Role',
                    body: $attendee->type->name,
                ),
            ]
        );

        $jwt = (new JWT([
            'iss' => $credentials->client_email,
            'key' => $credentials->private_key,
            'origins' => config('passes.google.origins'),
        ]))->addEventTicketObject($object)->sign();

        return redirect()->to('https://pay.google.com/gp/v/save/' . $jwt);
    }
}
