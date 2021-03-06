#!/usr/bin/env python3

"""Example of using API4AI personal protective equipment entrance detection."""

import os
import sys

import requests


# Use 'demo' mode just to try api4ai for free. Free demo is rate limited.
# For more details visit:
#   https://api4.ai
#
MODE = 'demo'


OPTIONS = {
    'demo': {
        'url': 'https://demo.api4ai.cloud/ppe-entrance/v1/results',
        'headers': {'A4A-CLIENT-APP-ID': 'sample'}
    }
}


if __name__ == '__main__':
    # Parse args.
    image = sys.argv[1] if len(sys.argv) > 1 else 'https://storage.googleapis.com/api4ai-static/samples/ppe-3.jpg'

    if '://' in image:
        # POST image via URL.
        response = requests.post(
            OPTIONS[MODE]['url'],
            headers=OPTIONS[MODE]['headers'],
            data={'url': image})
    else:
        # POST image as file.
        with open(image, 'rb') as image_file:
            response = requests.post(
                OPTIONS[MODE]['url'],
                headers=OPTIONS[MODE]['headers'],
                files={'image': (os.path.basename(image), image_file)}
            )

    # Print raw response.
    print(f'💬 Raw response:\n{response.text}\n')

    # Parse response and print people count, detected equipment and it's probability.
    objects = [x['entities'][1]['classes']
               for x in response.json()['results'][0]['entities'][0]['objects']]

    print(f'💬 Recognized persons: {len(objects)}\n')
    for num, obj in enumerate(objects, start=1):
        print(f"💬 Equipment of person and it's probabilities {num}:\n")
        for equip, probability in obj.items():
            print(f'{equip}: {probability}\n')
