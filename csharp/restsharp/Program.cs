using System;
using System.Net.Http;
using System.Text.Json.Nodes;

using MimeTypes;
using RestSharp;


/*
 * Use "demo" mode just to try api4ai for free. Free demo is rate limited.
 * For more details visit:
 *   https://api4.ai
 */

const String MODE = "demo";

String url;
Dictionary<String, String> headers = new Dictionary<String, String>();

switch (MODE) {
    case "demo":
        url = "https://demo.api4ai.cloud/ppe-entrance/v1/results";
        headers.Add("A4A-CLIENT-APP-ID", "sample");
        break;
    default:
        Console.WriteLine($"[e] Unsupported mode: {MODE}");
        return 1;
}

// Prepare request.
String image = args.Length > 0 ? args[0] : "https://storage.googleapis.com/api4ai-static/samples/ppe-3.jpg";
var client = new RestClient(new RestClientOptions(url) { ThrowOnAnyError = true });
var request = new RestRequest();
if (image.Contains("://")) {
    request.AddParameter("url", image);
} else {
    request.AddFile("image", image, MimeTypeMap.GetMimeType(Path.GetExtension(image)));
}
request.AddHeaders(headers);

// Perform request.
var jsonResponse = (await client.ExecutePostAsync(request)).Content!;

// Print raw response.
Console.WriteLine($"[i] Raw response:\n{jsonResponse}\n");

// Parse response and print people count, detected equipment and it's probability.
JsonNode docRoot = JsonNode.Parse(jsonResponse)!.Root;
var objects = from obj in docRoot["results"]![0]!["entities"]![0]!["objects"]!.AsArray()
              select obj!["entities"]![1]!["classes"]!;
Console.WriteLine($"[i] Recognized persons: {objects.Count()}\n");
foreach (var (obj, idx) in objects.Select((v, i) => (v, i))) {
    Console.WriteLine($"[i] Equipment of person and it's probabilities {idx + 1}:\n");
    Console.WriteLine(obj);
}

return 0;
